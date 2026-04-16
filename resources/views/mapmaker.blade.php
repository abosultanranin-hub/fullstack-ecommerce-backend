<!DOCTYPE html>
<html>
<head>
  <title>خريطة تفاعلية مع تحديث الموقع</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- روابط Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <!-- مكتبة Pusher -->
  <script src="https://js.pusher.com/7.2/pusher.min.js"></script>

  <style>
    #map {
      height: 500px;
      width: 100%;
    }
    body {
      font-family: Arial, sans-serif;
      direction: rtl;
      padding: 20px;
    }
  </style>
</head>
<body>

  <h3>📍 تتبع الموقع الحي</h3>
  <div id="map"></div>

  <script>
    // تمرير رقم الطلب من Laravel
    const orderId = {{ json_encode($orderId) }};

    if (!orderId) {
        alert("لا يوجد طلب لعرض موقعه.");
        throw new Error("Order ID غير موجود.");
    }

    // تهيئة الخريطة
    const map = L.map('map').setView([31.9522, 35.2332], 13); // الموقع الابتدائي

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // إنشاء ماركر الموقع
    let movingMarker = L.marker([31.9522, 35.2332]).addTo(map)
        .bindPopup('📍 الموقع الحالي')
        .openPopup();

    // تهيئة Pusher
    const pusher = new Pusher('7d3820021978e3cb19d4', {
      cluster: 'ap2',
      encrypted: true
    });

    // الاشتراك في القناة الخاصة لهذا الطلب
    const channel = pusher.subscribe(`private-ranin.${orderId}`);

    // الاستماع لتحديث الموقع
    channel.bind('location.updated', function(data) {
        console.log('📡 تم استلام الموقع الجديد:', data);

        const newLatLng = L.latLng(data.latitude, data.longitude);
        movingMarker.setLatLng(newLatLng);
        movingMarker.setPopupContent(`📍 الموقع الجديد:<br>${data.latitude}, ${data.longitude}<br>${new Date().toLocaleTimeString()}`);
        map.setView(newLatLng, 13);
    });

    // في حالة حدوث خطأ
    pusher.connection.bind('error', function(err) {
        console.error('❌ خطأ في الاتصال بـ Pusher:', err);
        alert("فشل الاتصال بالتتبع المباشر.");
    });
  </script>
</body>
</html>
