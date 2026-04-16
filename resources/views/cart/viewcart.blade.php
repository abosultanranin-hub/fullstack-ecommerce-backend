
<!doctype html>
<html lang="zxx">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>eCommerce HTML-5 Template </title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="manifest" href="site.webmanifest">
  <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/flaticon.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/slicknav.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/fontawesome-all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/themify-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

</head>

<body>

  <header>
    <!-- Header Start -->
   <div class="header-area">
        <div class="main-header ">
            <div class="header-top top-bg d-none d-lg-block">
               <div class="container-fluid">
                   <div class="col-xl-12">
                        <div class="row d-flex justify-content-between align-items-center">
                            <div class="header-info-left d-flex">
                                <div class="flag">
                                    <img src="assets/img/icon/header_icon.png" alt="">
                                </div>
                                <div class="select-this">
                                    <form action="#">
                                        <div class="select-itms">
                                            <select name="select" id="select1">
                                                <option value="">USA</option>
                                                <option value="">SPN</option>
                                                <option value="">CDA</option>
                                                <option value="">USD</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <ul class="contact-now">     
                                    <li>ranin</li>
                                </ul>
                            </div>
                            <div class="header-info-right">
                               <ul>                                          
                                   <li><a href="login.html">My Account </a></li>
                                   <li><a href="product_list.html">Wish List  </a></li>
                                   <li><a href="cart.html">Shopping</a></li>
                                   <li><a href="cart.html">Cart</a></li>
                                   <li><a href="checkout.html">Checkout</a></li>
                               </ul>
                            </div>
                        </div>
                   </div>
               </div>
            </div>
           <div class="header-bottom  header-sticky">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <!-- Logo -->
                        <div class="col-xl-1 col-lg-1 col-md-1 col-sm-3">
                            <div class="logo">
                              <a href="index.html"><img src="assets/img/logo/logo.png" alt=""></a>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-8 col-md-7 col-sm-5">
                            <!-- Main-menu -->
                            <div class="main-menu f-right d-none d-lg-block">
                                <nav>                                                
                                    <ul id="navigation">                                                                                                                                     
                                        <li><a href="index.html">Home</a></li>
                                        <li><a href="Catagori.html">Catagori</a></li>
                                        <li class="hot"><a href="#">Latest</a>
                                            <ul class="submenu">
                                                <li><a href="product_list.html"> Product list</a></li>
                                                <li><a href="single-product.html"> Product Details</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="blog.html">Blog</a>
                                            <ul class="submenu">
                                                <li><a href="blog.html">Blog</a></li>
                                                <li><a href="single-blog.html">Blog Details</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">Pages</a>
                                            <ul class="submenu">
                                                <li><a href="login.html">Login</a></li>
                                                <li><a href="cart.html">Card</a></li>
                                                <li><a href="elements.html">Element</a></li>
                                                <li><a href="about.html">About</a></li>
                                                <li><a href="confirmation.html">Confirmation</a></li>
                                                <li><a href="cart.html">Shopping Cart</a></li>
                                                <li><a href="checkout.html">Product Checkout</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="contact.html">Contact</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div> 
                        <div class="col-xl-5 col-lg-3 col-md-3 col-sm-3 fix-card">
                            <ul class="header-right f-right d-none d-lg-block d-flex justify-content-between">
                                <li class="d-none d-xl-block">
                                    <div class="form-box f-right ">
                                        <input type="text" name="Search" placeholder="Search products">
                                        <div class="search-icon">
                                            <i class="fas fa-search special-tag"></i>
                                        </div>
                                    </div>
                                 </li>
                                <li class=" d-none d-xl-block">
                                    <div class="favorit-items">
                                        <i class="far fa-heart"></i>
                                    </div>
                                </li>
                                <li>
                                    <div class="shopping-card">
                                        <a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
                                    </div>
                                </li>
                               <li class="d-none d-lg-block"> <a href="#" class="btn header-btn">Sign in</a></li>
                            </ul>
                        </div>
                        <!-- Mobile Menu -->
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
           </div>
        </div>
   </div>
    <!-- Header End -->
  </header>

  <!-- slider Area Start-->
  <div class="slider-area ">
    <!-- Mobile Menu -->
    <div class="single-slider slider-height2 d-flex align-items-center" data-background="assets/img/hero/category.jpg">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center">
                        <h2>Card List</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
  <!-- slider Area End-->

  <!--================Cart Area =================-->
  <section class="cart_area section_padding">
    <div class="container">
      <div class="cart_inner">
        <div class="table-responsive">
    <table class="table" id="cartTable">

            <thead>
              <tr>
                <th scope="col">Product</th>
                <th scope="col">Price</th>
                <th scope="col">Quantity</th>
                <th scope="col">Total</th>
                <th scope="col">Delete</th>

              </tr>
            </thead>
 <tbody>
            <!-- سيتم تعبئة الصفوف عبر jQuery -->
        </tbody>
          </table>
          <div class="checkout_btn_inner float-right">
            <a class="btn_1" href="#">Continue Shopping</a>
<a id="checkoutBtn" class="btn_1 checkout_btn_1" href="#">Proceed to checkout</a>
          </div>
        </div>
      </div>
  </section>
  <!--================End Cart Area =================-->

<footer>
    <!-- Footer Start-->
    <div class="footer-area footer-padding2">
        <div class="container">
            <div class="row d-flex justify-content-between">
                <div class="col-xl-3 col-lg-3 col-md-5 col-sm-6">
                   <div class="single-footer-caption mb-50">
                     <div class="single-footer-caption mb-30">
                          <!-- logo -->
                         <div class="footer-logo">
                             <a href="index.html"><img src="assets/img/logo/logo2_footer.png" alt=""></a>
                         </div>
                         <div class="footer-tittle">
                             <div class="footer-pera">
                                 <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore.</p>
                            </div>
                         </div>
                     </div>
                   </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-5">
                    <div class="single-footer-caption mb-50">
                        <div class="footer-tittle">
                            <h4>Quick Links</h4>
                            <ul>
                                <li><a href="#">About</a></li>
                                <li><a href="#"> Offers & Discounts</a></li>
                                <li><a href="#"> Get Coupon</a></li>
                                <li><a href="#">  Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-7">
                    <div class="single-footer-caption mb-50">
                        <div class="footer-tittle">
                            <h4>New Products</h4>
                            <ul>
                                <li><a href="#">Woman Cloth</a></li>
                                <li><a href="#">Fashion Accessories</a></li>
                                <li><a href="#"> Man Accessories</a></li>
                                <li><a href="#"> Rubber made Toys</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-5 col-sm-7">
                    <div class="single-footer-caption mb-50">
                        <div class="footer-tittle">
                            <h4>Support</h4>
                            <ul>
                             <li><a href="#">Frequently Asked Questions</a></li>
                             <li><a href="#">Terms & Conditions</a></li>
                             <li><a href="#">Privacy Policy</a></li>
                             <li><a href="#">Privacy Policy</a></li>
                             <li><a href="#">Report a Payment Issue</a></li>
                         </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer bottom -->
            <div class="row">
             <div class="col-xl-7 col-lg-7 col-md-7">
                 <div class="footer-copy-right">
                     <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
  Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="ti-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>                   </div>
             </div>
              <div class="col-xl-5 col-lg-5 col-md-5">
                 <div class="footer-copy-right f-right">
                     <!-- social -->
                     <div class="footer-social">
                         <a href="#"><i class="fab fa-twitter"></i></a>
                         <a href="#"><i class="fab fa-facebook-f"></i></a>
                         <a href="#"><i class="fab fa-behance"></i></a>
                         <a href="#"><i class="fas fa-globe"></i></a>
                     </div>
                 </div>
             </div>
         </div>
        </div>
    </div>

    <!-- Footer End-->
</footer>

<!-- JS here -->

   <!-- All JS Custom Plugins Link Here -->
<script src="{{ asset('assets/js/vendor/modernizr-3.5.0.min.js') }}"></script>

<!-- Jquery, Popper, Bootstrap -->
<script src="{{ asset('assets/js/vendor/jquery-1.12.4.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<!-- Jquery Mobile Menu -->
<script src="{{ asset('assets/js/jquery.slicknav.min.js') }}"></script>

<!-- Jquery Slick , Owl-Carousel Plugins -->
<script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/js/slick.min.js') }}"></script>

<!-- One Page, Animated-HeadLin -->
<script src="{{ asset('assets/js/wow.min.js') }}"></script>
<script src="{{ asset('assets/js/animated.headline.js') }}"></script>

<!-- Scrollup, nice-select, sticky -->
<script src="{{ asset('assets/js/jquery.scrollUp.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.sticky.js') }}"></script>
<script src="{{ asset('assets/js/jquery.magnific-popup.js') }}"></script>

<!-- contact js -->
<script src="{{ asset('assets/js/contact.js') }}"></script>
<script src="{{ asset('assets/js/jquery.form.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/js/mail-script.js') }}"></script>
<script src="{{ asset('assets/js/jquery.ajaxchimp.min.js') }}"></script>

<!-- Jquery Plugins, main Jquery -->	
<script src="{{ asset('assets/js/plugins.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

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
 $('#checkoutBtn').click(function () {
    window.location.href = "{{ route('checkout.index') }}";
});

        });
    </script>
</body>

</html>