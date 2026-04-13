const ChartLib = window.Chart;

let revenueChart;
let orderChart;

async function fetchJson(url, options = {}) {
  const response = await fetch(url, options);
  const payload = await response.json().catch(() => ({}));

  if (!response.ok || payload?.success === false) {
    const error = new Error(payload?.message || "Yêu cầu thất bại");
    error.status = response.status;
    throw error;
  }

  return payload;
}

function parseOrderDate(order) {
  const value = order.orderDate;
  if (!value) return null;

  const date = new Date(value);
  return Number.isNaN(date.getTime()) ? null : date;
}

function normalizeItem(item) {
  const nestedProduct = item?.product || {};
  const quantity = Number(item.quantity ?? item.qty ?? 0);
  const price = Number(nestedProduct.price ?? 0);

  return {
    id: String(nestedProduct.id ?? ""),
    name: nestedProduct.name || "San pham",
    category: nestedProduct.category || "Khac",
    quantity: Number.isFinite(quantity) && quantity > 0 ? quantity : 0,
    unitPrice: Number.isFinite(price) ? price : 0,
    lineRevenue: (Number.isFinite(price) ? price : 0) * (Number.isFinite(quantity) ? quantity : 0),
  };
}

function normalizeOrder(order) {
  const date = parseOrderDate(order);
  const status = String(order.status || "pending").toLowerCase();
  const isCancelled = status === "cancelled";
  const items = Array.isArray(order.items) ? order.items.map(normalizeItem) : [];
  const itemsRevenue = items.reduce((sum, item) => sum + item.lineRevenue, 0);

  const rawTotal = Number(order.totalAmount ?? order.total ?? order.subtotal ?? 0);
  const totalRevenue = Number.isFinite(rawTotal) && rawTotal > 0 ? rawTotal : itemsRevenue;

  return {
    status,
    isCancelled,
    date,
    items,
    totalRevenue,
    quantity: items.reduce((sum, item) => sum + item.quantity, 0),
  };
}

function formatCurrency(value) {
  return `${Number(value || 0).toLocaleString("vi-VN")}đ`;
}

function formatNumber(value) {
  return Number(value || 0).toLocaleString("vi-VN");
}

function toPercent(current, previous) {
  if (!previous) {
    return current > 0 ? 100 : 0;
  }

  return ((current - previous) / previous) * 100;
}

function setDeltaText(element, delta) {
  if (!element) return;

  const normalized = Number.isFinite(delta) ? delta : 0;
  const rounded = Math.abs(normalized).toFixed(1).replace(".", ",");
  const prefix = normalized >= 0 ? "+" : "-";

  element.innerText = `${prefix}${rounded}%`;
  element.classList.remove("text-green-500", "text-red-500");
  element.classList.add(normalized >= 0 ? "text-green-500" : "text-red-500");
}

function getDashboardMonths() {
  const start = new Date(2025, 10, 1);
  const now = new Date();
  const end = new Date(now.getFullYear(), now.getMonth(), 1);
  const months = [];
  const cursor = new Date(start);

  while (cursor <= end) {
    const date = new Date(cursor);
    const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}`;
    months.push({
      key,
      label: `${String(date.getMonth() + 1).padStart(2, "0")}/${date.getFullYear()}`,
      month: date.getMonth(),
      year: date.getFullYear(),
    });

    cursor.setMonth(cursor.getMonth() + 1);
  }

  return months;
}

function monthKey(date) {
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}`;
}

function buildCategoryOptions(products) {
  const select = document.getElementById("overviewCategoryFilter");
  if (!select) return;

  const categories = [...new Set(products.map((product) => product.category).filter(Boolean))];
  select.innerHTML = '<option value="all">Toan bo cua hang</option>';

  categories.forEach((category) => {
    const option = document.createElement("option");
    option.value = category;
    option.innerText = category;
    select.appendChild(option);
  });
}

function aggregateByMonth(orders, selectedCategory) {
  const months = getDashboardMonths();
  const revenueByMonth = Object.fromEntries(months.map((month) => [month.key, 0]));
  const orderCountByMonth = Object.fromEntries(months.map((month) => [month.key, 0]));

  orders.forEach((order) => {
    if (!order.date || order.isCancelled) return;

    const key = monthKey(order.date);
    if (!(key in revenueByMonth)) return;

    const matchedItems =
      selectedCategory === "all"
        ? order.items
        : order.items.filter((item) => item.category === selectedCategory);

    if (matchedItems.length === 0) return;

    const monthRevenue = matchedItems.reduce((sum, item) => sum + item.lineRevenue, 0);
    revenueByMonth[key] += monthRevenue;
    orderCountByMonth[key] += 1;
  });

  return {
    months,
    revenueSeries: months.map((month) => Math.round(revenueByMonth[month.key])),
    orderSeries: months.map((month) => orderCountByMonth[month.key]),
  };
}

function calculateTopProducts(orders, selectedCategory) {
  const months = getDashboardMonths();
  const monthKeys = months.map((month) => month.key);

  const currentMonthOrders = orders.filter((order) => {
    if (!order.date || order.isCancelled) return false;

    const key = monthKey(order.date);
    return key === monthKeys[monthKeys.length - 1];
  });

  const aggregate = new Map();

  currentMonthOrders.forEach((order) => {
    const matchedItems =
      selectedCategory === "all"
        ? order.items
        : order.items.filter((item) => item.category === selectedCategory);

    matchedItems.forEach((item) => {
      const mapKey = item.id || item.name;
      if (!aggregate.has(mapKey)) {
        aggregate.set(mapKey, {
          name: item.name,
          quantity: 0,
          revenue: 0,
        });
      }

      const bucket = aggregate.get(mapKey);
      bucket.quantity += item.quantity;
      bucket.revenue += item.lineRevenue;
    });
  });

  return [...aggregate.values()]
    .sort((a, b) => b.quantity - a.quantity)
    .slice(0, 5);
}

function renderTopProductsTable(topProducts) {
  const body = document.getElementById("topProductsBody");
  if (!body) return;

  if (topProducts.length === 0) {
    body.innerHTML = `
      <tr>
        <td colspan="4" class="text-center text-gray-500 py-8">Chưa có dữ liệu cho bộ lọc hiện tại</td>
      </tr>
    `;
    return;
  }

  body.innerHTML = topProducts
    .map(
      (item, index) => `
        <tr>
          <td>${index + 1}</td>
          <td>${item.name}</td>
          <td class="text-right">${formatNumber(item.quantity)}</td>
          <td class="text-right text-[#ff5a1f] font-semibold">${formatCurrency(item.revenue)}</td>
        </tr>
      `,
    )
    .join("");
}

function createRevenueChart(labels, values) {
  if (!ChartLib) return;

  const canvas = document.getElementById("doanhThu");
  if (!canvas) return;

  if (revenueChart) {
    revenueChart.destroy();
  }

  revenueChart = new ChartLib(canvas.getContext("2d"), {
    type: "line",
    data: {
      labels,
      datasets: [
        {
          label: "Doanh thu",
          data: values,
          borderColor: "#ff5a1f",
          backgroundColor: "#ff5a1f",
          borderWidth: 2,
          pointRadius: 3,
          pointHoverRadius: 5,
          pointBorderWidth: 2,
          pointBackgroundColor: "#ffffff",
          pointBorderColor: "#ff5a1f",
          tension: 0.35,
          fill: false,
        },
      ],
    },
    options: {
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: "bottom",
          labels: {
            color: "#ff5a1f",
            usePointStyle: true,
            pointStyle: "circle",
            boxWidth: 6,
            boxHeight: 6,
            padding: 16,
            font: {
              size: 14,
              family: "Inter",
            },
          },
        },
        tooltip: {
          callbacks: {
            label: (context) => `${context.dataset.label}: ${formatCurrency(context.parsed.y)}`,
          },
        },
      },
      scales: {
        x: {
          ticks: {
            color: "#6b7280",
            font: {
              size: 14,
              family: "Inter",
            },
          },
          grid: {
            color: "#d1d5db",
            borderDash: [4, 4],
          },
          border: {
            color: "#9ca3af",
          },
        },
        y: {
          beginAtZero: true,
          ticks: {
            color: "#4b5563",
            font: {
              size: 13,
              family: "Inter",
            },
            callback: (value) => Number(value).toLocaleString("vi-VN"),
          },
          grid: {
            color: "#d1d5db",
            borderDash: [4, 4],
          },
          border: {
            color: "#9ca3af",
          },
        },
      },
    },
  });
}

function createOrdersChart(labels, values) {
  if (!ChartLib) return;

  const canvas = document.getElementById("myChart");
  if (!canvas) return;

  if (orderChart) {
    orderChart.destroy();
  }

  orderChart = new ChartLib(canvas.getContext("2d"), {
    type: "bar",
    data: {
      labels,
      datasets: [
        {
          label: "Đơn hàng",
          data: values,
          backgroundColor: "#f2bb5a",
          borderRadius: 0,
          borderSkipped: false,
          barThickness: 62,
        },
      ],
    },
    options: {
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: "bottom",
          labels: {
            color: "#f2bb5a",
            boxWidth: 12,
            boxHeight: 12,
            padding: 16,
            font: {
              size: 14,
              family: "Inter",
            },
          },
        },
      },
      scales: {
        x: {
          ticks: {
            color: "#6b7280",
            font: {
              size: 14,
              family: "Inter",
            },
          },
          grid: {
            color: "#d1d5db",
            borderDash: [4, 4],
          },
          border: {
            color: "#9ca3af",
          },
        },
        y: {
          beginAtZero: true,
          ticks: {
            color: "#4b5563",
            font: {
              size: 13,
              family: "Inter",
            },
            callback: (value) => Number(value).toLocaleString("vi-VN"),
          },
          grid: {
            color: "#d1d5db",
            borderDash: [4, 4],
          },
          border: {
            color: "#9ca3af",
          },
        },
      },
    },
  });
}

function renderSummaryCards(orders, users) {
  const normalizedOrders = orders.filter((order) => !order.isCancelled);

  const totalRevenue = normalizedOrders.reduce((sum, order) => sum + order.totalRevenue, 0);
  const totalPurchase = normalizedOrders.reduce((sum, order) => sum + order.quantity, 0);

  const now = new Date();
  const currentMonth = now.getMonth();
  const currentYear = now.getFullYear();
  const previousMonthDate = new Date(currentYear, currentMonth - 1, 1);

  const thisMonthOrders = normalizedOrders.filter(
    (order) =>
      order.date &&
      order.date.getMonth() === currentMonth &&
      order.date.getFullYear() === currentYear,
  );

  const prevMonthOrders = normalizedOrders.filter(
    (order) =>
      order.date &&
      order.date.getMonth() === previousMonthDate.getMonth() &&
      order.date.getFullYear() === previousMonthDate.getFullYear(),
  );

  const thisMonthRevenue = thisMonthOrders.reduce((sum, order) => sum + order.totalRevenue, 0);
  const prevMonthRevenue = prevMonthOrders.reduce((sum, order) => sum + order.totalRevenue, 0);

  const thisMonthPurchase = thisMonthOrders.reduce((sum, order) => sum + order.quantity, 0);
  const prevMonthPurchase = prevMonthOrders.reduce((sum, order) => sum + order.quantity, 0);

  const isCurrentMonthUser = (user) => {
    const joinedAt = user.joinDate || user.joinedAt;
    if (!joinedAt) return false;

    const date = new Date(joinedAt);
    if (Number.isNaN(date.getTime())) return false;

    return date.getMonth() === currentMonth && date.getFullYear() === currentYear;
  };

  const isPreviousMonthUser = (user) => {
    const joinedAt = user.joinDate || user.joinedAt;
    if (!joinedAt) return false;

    const date = new Date(joinedAt);
    if (Number.isNaN(date.getTime())) return false;

    return (
      date.getMonth() === previousMonthDate.getMonth() &&
      date.getFullYear() === previousMonthDate.getFullYear()
    );
  };

  const thisMonthUsers = users.filter(isCurrentMonthUser).length;
  const prevMonthUsers = users.filter(isPreviousMonthUser).length;

  const totalRevenueValue = document.getElementById("totalRevenueValue");
  const totalPurchaseValue = document.getElementById("totalPurchaseValue");
  const newUsersValue = document.getElementById("newUsersValue");

  if (totalRevenueValue) totalRevenueValue.innerText = formatCurrency(totalRevenue);
  if (totalPurchaseValue) totalPurchaseValue.innerText = formatNumber(totalPurchase);
  if (newUsersValue) newUsersValue.innerText = `${thisMonthUsers > 0 ? "+" : ""}${formatNumber(thisMonthUsers)}`;

  setDeltaText(document.getElementById("totalRevenueDelta"), toPercent(thisMonthRevenue, prevMonthRevenue));
  setDeltaText(document.getElementById("totalPurchaseDelta"), toPercent(thisMonthPurchase, prevMonthPurchase));
  setDeltaText(document.getElementById("newUsersDelta"), toPercent(thisMonthUsers, prevMonthUsers));
}

function renderFilteredSection(orders, selectedCategory) {
  const { months, revenueSeries, orderSeries } = aggregateByMonth(orders, selectedCategory);
  const labels = months.map((month) => month.label);

  createRevenueChart(labels, revenueSeries);
  createOrdersChart(labels, orderSeries);

  const topProducts = calculateTopProducts(orders, selectedCategory);
  renderTopProductsTable(topProducts);
}

async function initOverviewCharts() {
  try {
    const [ordersPayload, usersPayload, productsPayload] = await Promise.all([
      fetchJson("/backend/api/orders.php?scope=all", { method: "GET", headers: { Accept: "application/json" } }),
      fetchJson("/backend/api/customers.php", { method: "GET", headers: { Accept: "application/json" } }),
      fetchJson("/backend/api/products.php", { method: "GET", headers: { Accept: "application/json" } }),
    ]);

    const rawOrders = Array.isArray(ordersPayload?.data) ? ordersPayload.data : [];
    const rawUsers = Array.isArray(usersPayload?.data) ? usersPayload.data : [];
    const rawProducts = Array.isArray(productsPayload?.data) ? productsPayload.data : [];

    const normalizedOrders = rawOrders.map(normalizeOrder).filter((order) => order.items.length > 0);
    renderSummaryCards(normalizedOrders, rawUsers);

    buildCategoryOptions(rawProducts);

    const select = document.getElementById("overviewCategoryFilter");
    const selectedCategory = select?.value || "all";
    renderFilteredSection(normalizedOrders, selectedCategory);

    select?.addEventListener("change", () => {
      renderFilteredSection(normalizedOrders, select.value || "all");
    });
  } catch (error) {
    if (error?.status === 401 || error?.status === 403) {
      window.location.href = "/src/pages/auth/login.php";
      return;
    }

    const topProductsBody = document.getElementById("topProductsBody");
    if (topProductsBody) {
      topProductsBody.innerHTML = `
        <tr>
          <td colspan="4" class="text-center text-red-500 py-8">Không thể tải dữ liệu tổng quan</td>
        </tr>
      `;
    }
  }
}

if (document.getElementById("doanhThu") || document.getElementById("myChart")) {
  initOverviewCharts();
}
