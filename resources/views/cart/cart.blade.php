<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />\

    <title>سلة المنتجات</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f5f5f5;
            padding: 30px;
        }
        .products {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .product {
            background: white;
            border-radius: 8px;
            padding: 20px;
            width: 220px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .product img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 6px;
        }
        .product h3 {
            margin: 10px 0;
        }
        .product p {
            color: #555;
        }
        .add-to-cart {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 16px;
        }
        #cartToggle {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            z-index: 1000;
        }
        #cartToggle::before {
            content: '🛒 ';
        }
        #cartCount {
            background: red;
            border-radius: 50%;
            padding: 3px 8px;
            margin-left: 5px;
            font-weight: bold;
            font-size: 14px;
            vertical-align: middle;
        }
        #cartContainer {
            display: none;
            background: #fff;
            padding: 20px;
            max-width: 400px;
            margin: 80px auto 0;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1000;
        }
        #cartContainer h3 {
            margin-top: 0;
            text-align: center;
        }
        #cartItems > div {
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        #cartTotal {
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
        }
        #viewCartDetails, #closeCart {
            margin-top: 10px;
            border: none;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        #viewCartDetails {
            background: #007bff;
            color: white;
        }
        #closeCart {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<!-- زر السلة -->
<button id="cartToggle">
<span id="cartCount">{{ $count }}</span>
</button>

<!-- عرض المنتجات -->
<div class="products">
    {{ __('test.اهلا بمتجر رنين') }}



@foreach ($products as $product)
    <div class="product">
        <img src="{{ $product->image_url ?? 'https://via.placeholder.com/200' }}" alt="{{ $product->name }}">
        <h3>{{ $product->name }}</h3>
        <p>السعر: {{ $product->price }}$</p>
        <button class="add-to-cart"
                data-id="{{ $product->id }}"
                data-name="{{ $product->name }}"
                data-price="{{ $product->price }}">
            إضافة إلى السلة
        </button>
    </div>
@endforeach
</div>

<!-- محتوى السلة -->
<div id="cartContainer">
    <h3>محتوى السلة</h3>
    <div id="cartItems"></div>
    <p id="cartTotal">$items->count()</p>

    <button id="viewCartDetails" style="display:none;">عرض تفاصيل السلة</button>
    <button id="closeCart">إغلاق</button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // إعداد التوكن للحماية
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>


<!-- كود AJAX -->
<script>
$(document).ready(function(){

    console.log("jQuery جاهز"); // أضف هذا لاختبار البداية

        // إضافة منتج للسلة
  $(document).on('click', '.add-to-cart', function () {
    let id = $(this).data('id');
    let name = $(this).data('name');
    let price = $(this).data('price');

    let currentCount = parseInt($('#cartCount').text());

    $.ajax({
        url: "/cart/add/" + id,
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
        _token: $('meta[name="csrf-token"]').attr('content'), // هنا نضيف التوكن
            id: id,
            name: name,
            price: price,
            quantity: 1
        },
       success: function (res) {
    if (res.success) {
        // دائماً نحدث العداد بناءً على المجموع الكامل للسلة
       $('#cartCount').text(res.total_count);
        console.log("تمت الإضافة بنجاح:", res);
    }
},

        error: function (xhr) {
            console.error("فشل الإضافة", xhr);
        }
    });
});


   


    // عرض محتوى السلة
    $('#cartToggle').on('click', function () {
        $.ajax({
            url: "{{ route('cart.view') }}",
            method: "GET",
            success: function (res) {
                $('#cartItems').empty();

                if (res.items.length === 0) {
                    $('#cartItems').html('<p>السلة فارغة</p>');
                    $('#cartTotal').text('المجموع: $0');
                    $('#viewCartDetails').hide();
                } else {
                    let total = 0;
                    res.items.forEach(item => {
                        let itemTotal = item.price * item.quantity;
                        total += itemTotal;
                        $('#cartItems').append(`
                            <div>
                                <strong>${item.name}</strong><br>
                                الكمية: ${item.quantity} × $${item.price}<br>
                                الإجمالي: $${itemTotal.toFixed(2)}
                            </div>
                        `);
                    });
                    $('#cartTotal').text('المجموع: $' + total.toFixed(2));
                    $('#viewCartDetails').show();
                }

                $('#cartContainer').fadeIn();
            },
            error: function () {
                alert("فشل تحميل السلة.");
            }
        });
    });

    // زر عرض تفاصيل السلة
    $('#viewCartDetails').on('click', function () {
        window.location.href = "{{ route('cart.details') }}";
    });

    // زر إغلاق محتوى السلة
    $('#closeCart').on('click', function () {
        $('#cartContainer').fadeOut();
    });

});


</script>

</body>
</html>


