<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إضافة موقع المستخدم</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            padding: 20px;
            background-color: #f0f0f0;
        }
        button {
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
        }
        #status {
            margin-top: 20px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>

    <h2>📍 تحديد موقعي</h2>
    <button id="get-location-btn" onclick="getLocation()">إضافة موقعي الحالي</button>
    <p id="status"></p>

    <script>
        async function getLocation() {
            const status = document.getElementById('status');
            const button = document.getElementById('get-location-btn');

            if (!navigator.geolocation) {
                status.textContent = "Geolocation غير مدعوم في متصفحك.";
                return;
            }

            status.textContent = "جاري جلب الموقع...";
            button.disabled = true;

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const { latitude, longitude } = position.coords;

                    try {
                        const response = await fetch('/save-location', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ latitude, longitude })
                        });

                        const text = await response.text();

                        console.log('🔍 الرد من السيرفر:', text); // 👈 لعرض الرد الكامل

                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            throw new Error("الرد ليس بصيغة JSON: " + text);
                        }

                        if (response.ok) {
                            status.textContent = `✅ ${data.message}`;
                            console.log('✅ تم الحفظ بنجاح:', data);
                        } else {
                            throw new Error(data.error || 'حدث خطأ غير متوقع أثناء حفظ الموقع');
                        }

                    } catch (error) {
                        status.textContent = `❌ خطأ: ${error.message}`;
                        console.error('❌ Error أثناء الإرسال:', error);
                    }

                    button.disabled = false;
                },
                (error) => {
                    let errorMessage = "⚠️ فشل جلب الموقع: ";
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage += "تم رفض الإذن من قبل المستخدم.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage += "معلومات الموقع غير متاحة.";
                            break;
                        case error.TIMEOUT:
                            errorMessage += "انتهت مهلة طلب الموقع.";
                            break;
                        default:
                            errorMessage += error.message;
                    }
                    status.textContent = errorMessage;
                    console.warn(errorMessage);
                    button.disabled = false;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }
    </script>

</body>
</html>
