<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل السلة</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            background: #f9f9f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f0f0f0;
        }
        input[type="number"] {
            width: 60px;
            padding: 5px;
            text-align: center;
        }
        .remove-btn {
            background: red;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #cartTotal {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }
        #checkoutBtn {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>تفاصيل السلة</h2>

    <table id="cartTable">
        <thead>
            <tr>
                <th>المنتج</th>
                <th>السعر</th>
                <th>الكمية</th>
                <th>الإجمالي</th>
                <th>حذف</th>
            </tr>
        </thead>
        <tbody>
            <!-- سيتم تعبئة الصفوف عبر jQuery -->
        </tbody>
    </table>

    <div id="cartTotal">المجموع: $0</div>

    <div style="text-align: left;">
        <button id="checkoutBtn">الدفع الآن</button>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- إعداد CSRF -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    </script>

    <!-- كود عرض السلة وتعديل الكمية والحذف والدفع -->
    <script>
        function loadCartDetails() {
            $.ajax({
                url: "{{ route('cart.details') }}",
                method: "GET",
                success: function (res) {
                    let total = 0;
                    $('#cartTable tbody').empty();

                    if (res.items.length === 0) {
                        $('#cartTable tbody').append('<tr><td colspan="5">السلة فارغة</td></tr>');
                        $('#cartTotal').text('المجموع: $0');
                        return;
                    }

                    res.items.forEach(item => {
                        const itemTotal = item.price * item.quantity;
                        total += itemTotal;

                        $('#cartTable tbody').append(`
                            <tr data-id="${item.products.id}">
                                <td>${item.products.name}</td>
                                <td>$${item.products.price}</td>
                                <td>
                                    <input type="number" class="quantity" value="${item.quantity}" min="1" data-id="${item.products.id}">
                                </td>
                                <td>$${itemTotal}</td>
                                <td>
                                    <button class="remove-btn">حذف</button>
                                </td>
                            </tr>
                        `);
                    });

                    $('#cartTotal').text('المجموع: $' + total);
                }
            });
        }

        $(document).ready(function () {
            loadCartDetails();

            // حذف منتج
            $(document).on('click', '.remove-btn', function () {
                const row = $(this).closest('tr');
                const id = row.data('id');

                $.ajax({
                    url: "{{ route('cart.remove') }}",
                    method: "POST",
                    data: { id },
                    success: function () {
                        row.remove();
                        loadCartDetails();
                    }
                });
            });

            // تعديل الكمية
            $(document).on('change', '.quantity', function () {
                const id = $(this).data('id');
                const quantity = $(this).val();
                $.ajax({
                    url: "{{ url('/cart/update') }}/" + id,
                    method: "POST",
                    data: {
                        id: id,
                        quantity: quantity
                    },
                    success: function () {
                        loadCartDetails();
                    },
                    error: function () {
                        alert('فشل تعديل الكمية');
                    }
                });
            });

            // زر الدفع
           // زر الدفع
$('#checkoutBtn').click(function () {
    window.location.href = "{{ route('checkout.index') }}";
});

        });
    </script>

</body>
</html>

