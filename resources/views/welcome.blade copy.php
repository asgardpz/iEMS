<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EV Charger Backend System</title>
  <link rel="icon" href="/favicon.ico" />
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <style>
    body {
      font-family: 'Noto Sans TC', sans-serif;
      margin: 0;
      background: #f3f4f6;
      color: #1f2937;
    }
    header {
      background: #1E2A38; /* 深藍 */
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    nav a {
      color: white;
      margin-left: 1rem;
      text-decoration: none;
      font-weight: 500;
    }
    nav a:hover {
      color: #00B2FF; /* 亮藍 hover */
    }
    .hero {
      text-align: center;
      padding: 3rem 2rem;
      background: linear-gradient(to right, #e0f2f1, #ffffff);
    }
    .hero h2 {
      color: #1E2A38;
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }
    .hero p {
      color: #374151;
      font-size: 1rem;
    }
    .charts {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 2rem;
      padding: 2rem;
    }
    .chart-card {
      background: #ffffff;
      padding: 1rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 300px;
      border: 1px solid #00B2FF; /* 亮藍框線 */
    }
    #map {
      height: 400px;
      margin: 2rem auto;
      width: 90%;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border: 2px solid #00B2FF;
    }
    .announcements {
      padding: 2rem;
      background: #fff;
      margin: 2rem auto;
      max-width: 700px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border-left: 4px solid #00B2FF;
    }
    .announcement-item {
      padding: 1rem;
      border-bottom: 1px solid #e5e7eb;
      transition: background 0.2s ease;
    }
    .announcement-item:last-child {
      border-bottom: none;
    }
    .announcement-item:hover {
      background: #f0f9ff; /* 淺藍 hover */
    }
    footer {
      text-align: center;
      padding: 2rem;
      background: #1E2A38;
      color: #ffffff;
      font-size: 0.875rem;
    }
    @media (max-width: 768px) {
      .charts {
        flex-direction: column;
        align-items: center;
      }
      #map {
        width: 95%;
      }
    }
      .btn-login {
    background-color: #00B2FF; /* 亮藍 */
    color: #ffffff;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.2s ease;
    }
    .btn-login:hover {
      background-color: #0095d6; /* hover 深藍藍 */
    }

    .btn-register {
      background-color: #00C853; /* 亮綠 */
      color: #ffffff;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.2s ease;
      margin-left: 0.5rem;
    }
    .btn-register:hover {
      background-color: #009624; /* hover 深綠 */
    }
    .wrap {
      display: flex;
      justify-content: center;
      align-items: stretch;
      padding: 3rem 2rem;
      background: #f9fafb;
      gap: 2rem;
    }
    .left {
      flex: 1;
      background: linear-gradient(135deg, #6fb6ff 0%, #1f4fb8 100%);
      color: #ffffff;
      border-radius: 12px;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .ev-icon {
      width: 100px;
      margin-bottom: 1rem;
    }
    .left-title {
      text-align: center;
      font-size: 1.5rem;
      font-weight: 700;
      line-height: 1.8rem;
      color: #7ef3a0;
    }
    .version {
      margin-top: 1rem;
      font-size: 0.9rem;
      color: #00B2FF; /* 亮藍版本號 */
    }
    .right {
      flex: 1;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 2rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .right h2 {
      color: #1E2A38;
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }
    .right p {
      color: #374151;
      font-size: 1rem;
      line-height: 1.5rem;
    }
    @media (max-width: 768px) {
      .wrap {
        flex-direction: column;
      }
      .left, .right {
        flex: none;
        width: 100%;
      }
    }
    .image-block img {
      width: 100%;
      height: auto;
      border-radius: 8px;
      object-fit: contain;
    }
    .carousel {
      position: relative;
      width: 100%;
      height: 100%;
      overflow: hidden;
    }
    .carousel-track {
      display: flex;
      transition: transform 0.5s ease;
    }
    .carousel-slide {
      min-width: 50%;
      padding: 10px;
    }
    .carousel-slide img {
      width: 100%;
      height: auto;
      border-radius: 8px;
    }
    .carousel-dots {
      display: flex;
      justify-content: center;
      margin-top: 10px;
    }
    .carousel-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: #ccc;
      margin: 0 5px;
      cursor: pointer;
    }
    .carousel-dot.active {
      background-color: #0078d4;
    }    
  </style>
</head>
<body>
  <header>
    <h1>EV Charger Backend System</h1>
    <nav>
      @auth
        <script>
          window.location.href = "{{ route('dashboard') }}";
        </script>
      @endauth
      <a href="{{ route('login') }}" class="btn-login">登入</a>
      <a href="{{ route('register') }}" class="btn-register">註冊</a>
    </nav>
  </header>
  <div class="wrap">
    <section class="left">
      <img src="{{ asset('assets/images/ev-icon.png') }}" alt="EV Icon" class="ev-icon" />
      <div class="left-title">
        <div class="line">EV CHARGER</div>
        <div class="line">BACKEND SYSTEM</div>
      </div>
     </section>

    <section class="right">
      <h2>智慧充電管理平台</h2>
      <p>即時監控、使用率分析、告警通知與能源數據，助你高效管理充電樁。</p>
    </section>
  </div>

  <section class="charts">
    <div class="chart-card">
      <h3>設備狀態</h3>
      <canvas id="statusChart"></canvas>
    </div>
    <div class="chart-card">
      <h3>使用率分析</h3>
      <canvas id="usageChart"></canvas>
    </div>
    <div class="chart-card">
      <h3>每日充電量</h3>
      <canvas id="energyChart"></canvas>
    </div>
  </section>

  <section>
    <h3 style="text-align:center;color:#004d40;">充電站分布</h3>
    <div id="map"></div>
  </section>

  <section class="announcements">
    <h3 style="color:#004d40;">最新公告</h3>
    <div class="announcement-item"><strong>2025/10/31</strong> - 對於10/23例行會議中說明頁面，提供完整彩圖。</div>
    <div class="announcement-item"><strong>2025/11/11、13</strong> - 請見標示「修改」、「新增」圖示，留言 comment 說明。</div>
    <div class="announcement-item"><strong>2025/11/19</strong> - 更新map示意。</div>
    <div class="announcement-item"><strong>2026/01/08</strong> - 12/31例行會議中說明頁面，提供完整彩圖。</div>
    <div class="announcement-item"><strong>2026/01/13</strong> - 12/31例行會議中說明頁面與增修，提供彩圖。</div>
    <div class="announcement-item"><strong>2026/01/15</strong> - 1/13初版會議中提到頁面調整，權限設定與帳號管理頁面編修。</div>
    <div class="announcement-item"><strong>2026/01/16</strong> - 1/16例行會議：搜尋會員時間限制示警視窗，會員註冊方式、會員自行登入編修資料。</div>
    <div class="announcement-item"><strong>2026/01/22</strong> - 新增六個功能icon。</div>
  </section>

      <!-- 左下 -->
    <div class="block image-block">
      <h2>充電站示意圖</h2>
      <img src="ev_station.jpg" alt="EV Station" />
    </div>

    <!-- 右下 -->
    <div class="block">
      <h2>圖片播放器</h2>
      <div class="carousel">
        <div class="carousel-track" id="carouselTrack">
          <div class="carousel-slide"><img src="img1.jpg" alt="Image 1" /></div>
          <div class="carousel-slide"><img src="img2.jpg" alt="Image 2" /></div>
          <div class="carousel-slide"><img src="img3.jpg" alt="Image 3" /></div>
          <div class="carousel-slide"><img src="img4.jpg" alt="Image 4" /></div>
          <div class="carousel-slide"><img src="img5.jpg" alt="Image 5" /></div>
          <div class="carousel-slide"><img src="img6.jpg" alt="Image 6" /></div>
          <div class="carousel-slide"><img src="img7.jpg" alt="Image 7" /></div>
          <div class="carousel-slide"><img src="img8.jpg" alt="Image 8" /></div>
          <div class="carousel-slide"><img src="img9.jpg" alt="Image 9" /></div>
          <div class="carousel-slide"><img src="img10.jpg" alt="Image 10" /></div>
        </div>
        <div class="carousel-dots" id="carouselDots"></div>
      </div>
    </div>

  <footer>
    © 2026 EV Charger Backend System | 版本 v1.0 | 聯絡: support@evcharger.com
  </footer>

  <script>
    new Chart(document.getElementById('statusChart'), {
      type: 'pie',
      data: {
        labels: ['線上', '離線'],
        datasets: [{
          data: [80, 20],
          backgroundColor: ['#1E2A38', '#00B2FF']
        }]
      }
    });
    new Chart(document.getElementById('usageChart'), {
      type: 'bar',
      data: {
        labels: ['站點A', '站點B', '站點C'],
        datasets: [{
          label: '使用率 %',
          data: [65, 45, 30],
          backgroundColor: '#1E2A38'
        }]
      }
    });
    new Chart(document.getElementById('energyChart'), {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'kWh',
          data: [120, 150, 180, 130, 170, 200, 220],
          borderColor: '#1E2A38',
          fill: false
        }]
      }
    });

    var map = L.map('map').setView([25.0478, 121.5319], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(map);
    L.marker([25.0478, 121.5319]).addTo(map).bindPopup("站點 A - 可用 5 台");
    L.marker([25.0378, 121.5639]).addTo(map).bindPopup("站點 B - 可用 3 台");
    L.marker([25.0578, 121.5239]).addTo(map).bindPopup("站點 C - 可用 2 台");

    // 圖片播放器邏輯
    const track = document.getElementById('carouselTrack');
    const dotsContainer = document.getElementById('carouselDots');
    const totalSlides = 10;
    const slidesPerPage = 2;
    const totalPages = Math.ceil(totalSlides / slidesPerPage);
    let currentPage = 0;

    function updateCarousel() {
      track.style.transform = `translateX(-${currentPage * 100}%)`;
      Array.from(dotsContainer.children).forEach((dot, i) => {
        dot.classList.toggle('active', i === currentPage);
      });
    }

    for (let i = 0; i < totalPages; i++) {
      const dot = document.createElement('div');
      dot.classList.add('carousel-dot');
      if (i === 0) dot.classList.add('active');
      dot.addEventListener('click', () => {
        currentPage = i;
        updateCarousel();
      });
      dotsContainer.appendChild(dot);
    }

    updateCarousel();    
  </script>
</body>
</html>

