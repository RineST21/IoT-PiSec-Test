<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }
    body {
      background: #f0f2f5;
      margin: 0;
      padding: 20px;
    }
    .dashboard-container {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      max-width: 800px;
      margin: auto;
    }
    h1 {
      text-align: center;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
    }
    th {
      background: #007BFF;
      color: #fff;
      text-align: center;
    }
    td.numeric {
      text-align: center;
    }
    tr:hover {
      background: #f1f1f1;
    }
    .logout {
      display: block;
      text-align: center;
      margin-top: 20px;
    }
    .logout button {
      background: #dc3545;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
    }
    .logout button:hover {
      background: #c82333;
    }
    .pagination {
      text-align: center;
      margin-top: 20px;
    }
    .pagination button {
      background: #007BFF;
      color: #fff;
      border: none;
      padding: 8px 12px;
      margin: 0 4px;
      border-radius: 4px;
      cursor: pointer;
    }
    .pagination button.active {
      background: #0056b3;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h1>Sensor dashboard</h1>
    <table id="sensorTable">
      <thead>
        <tr>
          <th>Data</th>
          <th>Temperature (°C)</th>
          <th>Humidity (%RH)</th>
          <th>Pressure (hPa)</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="4">Ładowanie danych...</td></tr>
      </tbody>
    </table>
    <div class="pagination"></div>
    <div class="logout">
      <form action="logout.php" method="post">
        <button type="submit">Logout</button>
      </form>
    </div>
  </div>
  <script>
    let sensorData = [];
    let currentPage = 1;
    const rowsPerPage = 10;

    function renderTable() {
      const tbody = document.querySelector('#sensorTable tbody');
      tbody.innerHTML = '';

      const totalPages = Math.ceil(sensorData.length / rowsPerPage);
      const startIndex = (currentPage - 1) * rowsPerPage;
      const pageData = sensorData.slice(startIndex, startIndex + rowsPerPage);

      if (pageData.length > 0) {
        pageData.forEach(row => {
          const tr = document.createElement('tr');
          tr.innerHTML = `<td>${row.Date}</td>
                          <td class="numeric">${row.Temperature}</td>
                          <td class="numeric">${row.Humidity}</td>
                          <td class="numeric">${row.Pressure}</td>`;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = "<tr><td colspan='4'>Brak danych</td></tr>";
      }
      renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
      const paginationDiv = document.querySelector('.pagination');
      paginationDiv.innerHTML = '';

      for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === currentPage) {
          btn.classList.add('active');
        }
        btn.addEventListener('click', () => {
          currentPage = i;
          renderTable();
        });
        paginationDiv.appendChild(btn);
      }
    }

    function fetchData() {
      fetch('data.php')
        .then(response => {
          if (!response.ok) throw new Error('Błąd sieci');
          return response.json();
        })
        .then(data => {
          sensorData = data;
          const totalPages = Math.ceil(sensorData.length / rowsPerPage);
          if (currentPage > totalPages) {
            currentPage = totalPages;
          }
          renderTable();
        })
        .catch(error => console.error('Błąd pobierania danych:', error));
    }
    
    setInterval(fetchData, 5000);
    fetchData();
  </script>
</body>
</html>
