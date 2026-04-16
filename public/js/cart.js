
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سلة التسوق باستخدام Ajax</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .products {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            justify-content: center;
        }
        .product {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            width: 240px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        .product img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .product h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 18px;
        }
        .product p {
            margin: 0 0 15px 0;
            color: #666;
            font-size: 16px;
        }
        .cart-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
            transition: background 0.3s;
        }
        .cart-btn:hover {
            background: #218838;
        }
        .cart-container {
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            display: none;
            z-index: 1000;
            width: 350px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .cart-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #343a40;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            z-index: 1001;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }
        .cart-toggle:hover {
            background: #23272b;
        }
        .cart-toggle::before {
            content: "🛒";
            font-size: 18px;
        }
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .cart-header h2 {
            margin: 0;
            color: #333;
            font-size: 22px;
        }
        .close-cart {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #666;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        .cart-item-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .cart-item-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .cart-item-name {
            font-weight: 500;
            color: #333;
            margin-bottom: 3px;
        }
        .cart-item-price {
            color: #28a745;
            font-weight: bold;
        }
        .cart-item-quantity {
            color: #666;
            font-size: 14px;
        }
        .cart-total {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }
        .empty-cart {
            text-align: center;
            padding: 30px 0;
            color: #666;
            font-size: 16px;
        }
        .checkout-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 15px;
            transition: background 0.3s;
        }
        .checkout-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
    
        <button class="cart-toggle" id="cartToggle">
            <span id="cartCount">0</span>
        </button>


        
        <div class="cart-container" id="cartContainer">
            <div class="cart-header">
                <h2>سلة التسوق</h2>
                <button class="close-cart" id="closeCart">✕</button>
            </div>
            <div id="cartItems">
                <p class="empty-cart" id="emptyCart">السلة فارغة</p>
            </div>
            <div class="cart-total" id="cartTotal">المجموع: $0</div>
            <button class="checkout-btn">إتمام الشراء</button>
        </div>
        <h1 style="text-align: center; margin-bottom: 30px; color: #333;">منتجاتنا</h1>
        <div class="products">
            <div class="product">
                <img src="https://via.placeholder.com/200" alt="منتج 1">
                <h3>منتج 1</h3>
                <p>السعر: $10</p>
                <button class="cart-btn" data-id="1" data-name="منتج 1" data-price="10" data-image="https://via.placeholder.com/200">إضافة إلى السلة</button>
            </div>
            <div class="product">
                <img src="https://via.placeholder.com/200" alt="منتج 2">
                <h3>منتج 2</h3>
                <p>السعر: $15</p>
                <button class="cart-btn" data-id="2" data-name="منتج 2" data-price="15" data-image="https://via.placeholder.com/200">إضافة إلى السلة</button>
            </div>
            <div class="product">
                <img src="https://via.placeholder.com/200" alt="منتج 3">
                <h3>منتج 3</h3>
                <p>السعر: $20</p>
                <button class="cart-btn" data-id="3" data-name="منتج 3" data-price="20" data-image="https://via.placeholder.com/200">إضافة إلى السلة</button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js">









<script>
عرض محتويات السلة
    $(document).ready(function () {
        $('#cartToggle').on('click', function () {
            $.ajax({
                url: "{{ route('cart.index') }}",
                method: "GET",
                success: function (response) {
                    $('#cartItems').empty();
                    if (response.items.length === 0) {
                        $('#cartItems').html('<p class="empty-cart">السلة فارغة</p>');
                        $('#cartTotal').text('المجموع: $0');
                    } else {
                        let total = 0;
                        response.items.forEach(item => {
                            total += item.quantity * item.price;
                            $('#cartItems').append(`
                                <div class="cart-item">
                                    <div class="cart-item-info">
                                        <img src="${item.image ?? 'https://via.placeholder.com/50'}" class="cart-item-img">
                                        <div>
                                            <div class="cart-item-name">${item.name}</div>
                                            <div class="cart-item-quantity">الكمية: ${item.quantity}</div>
                                        </div>
                                    </div>
                                    <div class="cart-item-price">$${item.price}</div>
                                </div>
                            `);
                        });
                        $('#cartTotal').text('المجموع: $' + total);
                    }
                    $('#cartContainer').fadeIn();
                },
                error: function () {
                    alert('حدث خطأ أثناء تحميل السلة');
                }
            });
        });
        $('#closeCart').on('click', function () {
            $('#cartContainer').fadeOut();
        });
    });


    بدي كود ajax اضيفه  على الكود السابق  بقول كل ما اعمل on click على الاضافة الي السلة يروح علىroure add to cart  as ajax request و بدون تحديث الصفحة بزداد ال count بواحد



    $('.cart-btn').on('click', function () {
    let button = $(this);
    let productId = button.data('id');
    let productName = button.data('name');
    let productPrice = button.data('price');
    let productImage = button.data('image');
    $.ajax({
        url: "{{ route('cart.add') }}", // Laravel route name (GET or POST حسب تعريفك)
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}", // مهم في Laravel لحماية CSRF
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            quantity: 1 // يمكن تعديله لاحقاً حسب الحاجة
        },
        success: function (response) {
            // زيادة العداد في الزر
            let currentCount = parseInt($('#cartCount').text());
            $('#cartCount').text(currentCount + 1);
        },
        error: function () {
            alert("حدث خطأ أثناء إضافة المنتج إلى السلة");
        }
    });
});



 واذا تم الضعط على عرض  السلة بدي يتم اظهار المنتج الجديد من غير تحديث لال append
$('#cartToggle').on('click', function () {
    $.ajax({
        url: "{{ route('cart.index') }}",
        method: "GET",
        success: function (response) {
            $('#cartItems').empty(); // يمسح القديم
            if (response.items.length === 0) {
                $('#cartItems').html('<p class="empty-cart">السلة فارغة</p>');
                $('#cartTotal').text('المجموع: $0');
            } else {
                let total = 0;
                response.items.forEach(item => {
                    total += item.quantity * item.price;
                    $('#cartItems').append(`
                        <div class="cart-item">
                            <div class="cart-item-info">
                                <img src="${item.image ?? 'https://via.placeholder.com/50'}" class="cart-item-img">
                                <div>
                                    <div class="cart-item-name">${item.name}</div>
                                    <div class="cart-item-quantity">الكمية: ${item.quantity}</div>
                                </div>
                            </div>
                            <div class="cart-item-price">$${item.price}</div>
                        </div>
                    `);
                });
                $('#cartTotal').text('المجموع: $' + total);
            }
            $('#cartContainer').fadeIn(); // يظهر السلة
        }
    });
});

</script>
    </script>
</body>
</html>
