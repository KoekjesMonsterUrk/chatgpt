<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 h-screen">
  <div class="flex h-full">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex flex-col">
      <div class="flex items-center justify-center h-20 shadow-md">
        <h1 class="text-2xl font-bold">Admin Panel</h1>
      </div>
      <nav class="flex-1 px-4 py-8">
        <ul>
          <li class="mb-4">
            <a href="#" class="flex items-center text-gray-300 hover:text-white">
              <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 20c5.523 0 10-4.477 10-10S15.523 0 10 0 0 4.477 0 10s4.477 10 10 10zM8 7h4V6a1 1 0 10-2 0v1zm-2 4a2 2 0 110-4 2 2 0 010 4zm6 0a2 2 0 110-4 2 2 0 010 4z"/></svg>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="mb-4">
            <a href="#" class="flex items-center text-gray-300 hover:text-white">
              <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 20c5.523 0 10-4.477 10-10S15.523 0 10 0 0 4.477 0 10s4.477 10 10 10zM7 9a1 1 0 100 2h6a1 1 0 100-2H7z"/></svg>
              <span>Users</span>
            </a>
          </li>
          <li class="mb-4">
            <a href="#" class="flex items-center text-gray-300 hover:text-white">
              <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 20c5.523 0 10-4.477 10-10S15.523 0 10 0 0 4.477 0 10s4.477 10 10 10zM8 7h4V6a1 1 0 10-2 0v1zm-2 4a2 2 0 110-4 2 2 0 010 4zm6 0a2 2 0 110-4 2 2 0 010 4z"/></svg>
              <span>Settings</span>
            </a>
          </li>
          <li class="mb-4">
            <a href="#" class="flex items-center text-gray-300 hover:text-white">
              <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 20c5.523 0 10-4.477 10-10S15.523 0 10 0 0 4.477 0 10s4.477 10 10 10zM8 7h4V6a1 1 0 10-2 0v1zm-2 4a2 2 0 110-4 2 2 0 010 4zm6 0a2 2 0 110-4 2 2 0 010 4z"/></svg>
              <span>Reports</span>
            </a>
          </li>
        </ul>
      </nav>
      <div class="p-4">
        <button class="w-full bg-red-500 hover:bg-red-700 text-white py-2 rounded">Logout</button>
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="flex items-center justify-between h-16 bg-white shadow px-6">
        <div class="text-xl font-semibold">Dashboard</div>
        <div class="flex items-center">
          <input type="text" placeholder="Search..." class="px-4 py-2 border rounded-lg">
          <button class="ml-4 p-2 bg-gray-800 text-white rounded-lg">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 20c5.523 0 10-4.477 10-10S15.523 0 10 0 0 4.477 0 10s4.477 10 10 10zM8 7h4V6a1 1 0 10-2 0v1zm-2 4a2 2 0 110-4 2 2 0 010 4zm6 0a2 2 0 110-4 2 2 0 010 4z"/></svg>
          </button>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 p-6 overflow-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
          <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Overview</h2>
            <p class="text-gray-600">This is the overview section.</p>
          </div>
          <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Details</h2>
            <p class="text-gray-600">This is the details section.</p>
          </div>
          <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Statistics</h2>
            <canvas id="statisticsChart" class="mt-4"></canvas>
          </div>
          <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-2">Reports</h2>
            <canvas id="reportsChart" class="mt-4"></canvas>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
          <h2 class="text-lg font-semibold mb-4">Sales Overview</h2>
          <canvas id="salesChart"></canvas>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4">User List</h2>
          <div class="overflow-auto">
            <table class="min-w-full bg-white">
              <thead>
                <tr>
                  <th class="py-2 px-4 border-b-2">ID</th>
                  <th class="py-2 px-4 border-b-2">Name</th>
                  <th class="py-2 px-4 border-b-2">Email</th>
                  <th class="py-2 px-4 border-b-2">Role</th>
                  <th class="py-2 px-4 border-b-2">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="py-2 px-4 border-b">1</td>
                  <td class="py-2 px-4 border-b">John Doe</td>
                  <td class="py-2 px-4 border-b">john.doe@example.com</td>
                  <td class="py-2 px-4 border-b">Admin</td>
                  <td class="py-2 px-4 border-b text-green-500">Active</td>
                </tr>
                <tr>
                  <td class="py-2 px-4 border-b">2</td>
                  <td class="py-2 px-4 border-b">Jane Smith</td>
                  <td class="py-2 px-4 border-b">jane.smith@example.com</td>
                  <td class="py-2 px-4 border-b">User</td>
                  <td class="py-2 px-4 border-b text-red-500">Inactive</td>
                </tr>
                <!-- Add more users as needed -->
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Statistics Chart
      var ctx = document.getElementById('statisticsChart').getContext('2d');
      if (!window.statisticsChart) {
        window.statisticsChart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: ['Red', 'Blue', 'Yellow'],
            datasets: [{
              label: '# of Votes',
              data: [12, 19, 3],
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)'
              ],
              borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false
          }
        });
      }

      // Reports Chart
      var ctx2 = document.getElementById('reportsChart').getContext('2d');
      if (!window.reportsChart) {
        window.reportsChart = new Chart(ctx2, {
          type: 'bar',
          data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'],
            datasets: [{
              label: 'Sales',
              data: [12, 19, 3, 5, 2, 3],
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
              ],
              borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false
          }
        });
      }

      // Sales Overview Chart
      var ctx3 = document.getElementById('salesChart').getContext('2d');
      if (!window.salesChart) {
        window.salesChart = new Chart(ctx3, {
          type: 'line',
          data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'],
            datasets: [{
              label: 'Revenue',
              data: [65, 59, 80, 81, 56, 55],
              fill: false,
              borderColor: 'rgba(75, 192, 192, 1)',
              tension: 0.1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false
          }
        });
      }
    });
  </script>
</body>
</html>
