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
      flex: 2;
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
      flex: 8;
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
      height: calc(100% - 30px); /* 保留空間給下方點 */
    }
    .carousel-slide {
      min-width: 50%;
      padding: 10px;
      position: relative;
    }
    .carousel-slide img {
      width: 100%;
      height: 100%;
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
    .wrap2 {
      display: flex;
      justify-content: center;
      align-items: stretch;
      padding: 3rem 2rem;
      background: #f9fafb;
      gap: 2rem;
      height: 340px;
    }

    .left2 {
      flex: 4;
      padding: 0;
      margin: 0;
      height: 340px; /* 固定高度，與 wrap2 對齊 */
      overflow: hidden;
    }

    .left2 img {
      width: 100%;
      height: 100%;
      object-fit: cover; /* 填滿容器，略裁切但無空白 */
      display: block;
      border-radius: 0; /* 若不想留白就設為 0 */
    }

    .right2 {
      flex: 6;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 2rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    @media (max-width: 768px) {
      .wrap2 {
        flex-direction: column;
      }
      .left2, .right2 {
        flex: none;
        width: 100%;
      }
    }

    .caption {
      position: absolute;
      bottom: 10px;
      left: 10px;
      right: 10px;
      background: rgba(0,0,0,0.5); /* 半透明黑底 */
      color: #fff;
      padding: 8px 12px;
      border-radius: 6px;
      font-size: 0.9rem;
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
      <a href="{{ route('login') }}" class="btn-login">Log In</a>
      <a href="{{ route('register') }}" class="btn-register">Sign Up</a>
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
      <p id="typingText"></p>
    </section>

  </div>
  
  <div class="wrap2">
    <section class="left2">
      <img src="{{ asset('assets/images/ev-index.png') }}" alt="EV Full Image" />
    </section>

    <section class="right2">
      <div class="carousel">
        <div class="carousel-track" id="carouselTrack">
          <div class="carousel-slide"><img src="{{ asset('assets/images/img1.png') }}" />
            <div class="caption">Transactions & Billing</div>
          </div>
          <div class="carousel-slide"><img src="{{ asset('assets/images/img2.png') }}" />
            <div class="caption">Real Time Monitoring</div>
          </div>
          <div class="carousel-slide"><img src="{{ asset('assets/images/img3.png') }}" />
            <div class="caption">Dashboard</div>
          </div>
          <div class="carousel-slide"><img src="{{ asset('assets/images/img4.png') }}" />
            <div class="caption">Reports & Analytics</div>
          </div>
          <div class="carousel-slide"><img src="{{ asset('assets/images/img5.png') }}" />
            <div class="caption">Device Management</div>
          </div>
          <div class="carousel-slide"><img src="{{ asset('assets/images/img6.png') }}" />
            <div class="caption">Maintenance Management</div>
          </div>
          <div class="carousel-slide"><img src="{{ asset('assets/images/img7.png') }}" />
            <div class="caption">Work Order Management</div>
          </div>
          <div class="carousel-slide"><img src="{{ asset('assets/images/img8.png') }}" />
            <div class="caption">Member Management</div>
          </div>
          <div class="carousel-slide"><img src="{{ asset('assets/images/img9.png') }}" />
            <div class="caption">Permission Settings</div>
          </div>
          <div class="carousel-slide"><img src="{{ asset('assets/images/img10.png') }}" />
            <div class="caption">Account</div>
          </div>
        </div>
        <div class="carousel-dots" id="carouselDots"></div>
      </div>
    </section>
  </div>
  
  <footer>
    © 2026 EV Charger Backend System | Version v1.0 | Email: support@evcharger.com
  </footer>

  <script>
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

    // ✅ 每 5 秒自動切換一次
    setInterval(() => {
      currentPage = (currentPage + 1) % totalPages;
      updateCarousel();
    }, 5000);

    const text = `Our smart charging platform integrates real-time monitoring, usage analytics, alert notifications, and energy data visualization. 
    It supports dynamic load balancing, AI-driven scheduling, and grid-friendly charging strategies to optimize station performance. 
    Designed for fleet operators, property managers, and energy providers, the system helps reduce operational costs, extend battery life, 
    and ensure reliable, scalable EV infrastructure across urban and commercial environments.`;

    let index = 0;
    const typingElement = document.getElementById("typingText");

    function typeEffect() {
      if (index < text.length) {
        typingElement.innerHTML += text.charAt(index);
        index++;
        setTimeout(typeEffect, 30); // 每 30ms 打一個字，可調快慢
      }
    }

    typeEffect();

  </script>
</body>
</html>

