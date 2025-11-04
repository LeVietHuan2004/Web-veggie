$(document).ready(function () {
    /***********************************************************
     * PAGE LOGIN, REGISTER
     ***********************************************************/

    //Validate register form
    $("#register-form").submit(function (e) {
        let name = $('input[name="name"]').val();
        let email = $('input[name="email"]').val();
        let password = $('input[name="password"]').val();
        let confirmPassword = $('input[name="confirmPassword"]').val();
        let checkbox1 = $('input[name="checkbox1"]').is(":checked");
        let checkbox2 = $('input[name="checkbox2"]').is(":checked");
        let errorMessage = "";

        if (name.length < 3) {
            errorMessage += "Họ và tên phải có ít nhất 3 ký tự. <br>";
        }

        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            errorMessage += "Email không hợp lệ. <br>";
        }

        if (password.length < 6) {
            errorMessage += "Mật khẩu phải có ít nhất 6 ký tự.<br>";
        }

        if (password != confirmPassword) {
            errorMessage += "Mật khẩu nhập lại không khớp. <br>";
        }

        if (!checkbox1 || !checkbox2) {
            errorMessage +=
                "Bạn phải đồng ý với các điều khoản trước khi tạo tài khoản.<br>";
        }

        if (errorMessage != "") {
            toastr.error(errorMessage, "Lỗi");
            e.preventDefault();
        }
    });

    //Validate login form
    $("#login-form").submit(function (e) {
        toastr.clear();
        let email = $('input[name="email"]').val();
        let password = $('input[name="password"]').val();
        let errorMessage = "";

        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            errorMessage += "Email không hợp lệ. <br>";
        }

        if (password.length < 6) {
            errorMessage += "Mật khẩu phải có ít nhất 6 ký tự.<br>";
        }

        if (errorMessage != "") {
            toastr.error(errorMessage, "Lỗi");
            e.preventDefault();
        }
    });

    //Validate resetPassword form
    $("#reset-password-form").submit(function (e) {
        let email = $('input[name="email"]').val();
        let password = $('input[name="password"]').val();
        let confirmPassword = $('input[name="password_confirmation"]').val();

        let errorMessage = "";

        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            errorMessage += "Email không hợp lệ. <br>";
        }

        if (password.length < 6) {
            errorMessage += "Mật khẩu phải có ít nhất 6 ký tự.<br>";
        }
        if (password != confirmPassword) {
            errorMessage += "Mật khẩu nhập lại không khớp. <br>";
        }

        if (errorMessage != "") {
            toastr.error(errorMessage, "Lỗi");
            e.preventDefault();
        }
    });

    /***********************************************************
     * PAGE ACCOUNT
     ***********************************************************/

    //When clicking on the image => open input file
    $(".profile-pic").click(function () {
        $("#avatar").click();
    });

    //When selecting a image => display preview image
    $("#avatar").change(function () {
        let input = this;
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#update-account").on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        let urlUpdate = $(this).attr("action");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: urlUpdate,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(".btn-wrapper button")
                    .text("Đang cập nhật...")
                    .attr("disabled", true);
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    //Update new Image
                    if (response.avatar) {
                        $("#preview-image").attr("src", response.avatar);
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    toastr.error(value[0]);
                });
            },
            complete: function () {
                $(".btn-wrapper button")
                    .text("Cập nhật")
                    .attr("disabled", false);
            },
        });
    });

    //Change password  form
    $("#change-password-form").submit(function (e) {
        e.preventDefault();
        let current_password = $('input[name="current_password"]').val().trim();
        let new_password = $('input[name="new_password"]').val().trim();
        let confirm_new_password = $('input[name="confirm_new_password"]')
            .val()
            .trim();

        let errorMessage = "";

        if (current_password.length < 6) {
            errorMessage += "Mật khẩu cũ phải có ít nhất 6 ký tự.<br>";
        }
        if (new_password.length < 6) {
            errorMessage += "Mật khẩu mới phải có ít nhất 6 ký tự.<br>";
        }
        if (new_password != confirm_new_password) {
            errorMessage += "Mật khẩu nhập lại không khớp. <br>";
        }

        if (errorMessage != "") {
            toastr.error(errorMessage, "Lỗi");
            return;
        }

        let formData = $(this).serialize();
        let urlUpdate = $(this).attr("action");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: urlUpdate,
            type: "POST",
            data: formData,
            beforeSend: function () {
                $(".btn-wrapper button")
                    .text("Đang cập nhật...")
                    .attr("disabled", true);
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $("#change-password-form")[0].reset();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    toastr.error(value[0]);
                });
            },
            complete: function () {
                $(".btn-wrapper button")
                    .text("Cập nhật")
                    .attr("disabled", false);
            },
        });
    });

    //Validate form address
    $("#addAdressForm").submit(function (e) {
        e.preventDefault();

        let isValid = true;

        //Delete old error notification
        $(".error-message").remove();

        let fullName = $("#full_name").val().trim();
        let phone = $("#phone").val().trim();

        if (fullName.length < 3) {
            isValid = false;
            $("#full_name").after(
                '<p class = "error-message text-danger">Họ và tên không được ít hơn 3 ký tự. </p>'
            );
        }

        let phoneRegex = /^[0-9]{10,11}$/;
        if (!phoneRegex.test(phone)) {
            isValid = false;
            $("#phone").after(
                '<p class = "error-message text-danger">Số điện thoại không hợp lệ. </p>'
            );
        }

        if (isValid) {
            this.submit();
        }
    });

    /***********************************************************
     * PAGE PRODUCTS
     ***********************************************************/
    let currentPage = 1;
    $(document).on("click", ".pagination-link", function (e) {
        e.preventDefault();
        let pageUrl = $(this).attr("href");
        let page = pageUrl.split("page=")[1];
        currentPage = page;
        fetchProducts();
    });

    //Product load function (combining filter + pagination)
    function fetchProducts() {
        let category_id = $(".category-filter.active").data("id") || "";
        let minPrice = $(".slider-range").slider("values", 0);
        let maxPrice = $(".slider-range").slider("values", 1);
        let sort_by = $("#sort-by").val();

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "products/filter?page=" + currentPage,
            type: "GET",
            data: {
                category_id: category_id,
                min_price: minPrice,
                max_price: maxPrice,
                sort_by: sort_by,
            },
            beforeSend: function () {
                $("#loading-spinner").show();
                $("#liton_product_grid").hide();
            },
            success: function (response) {
                $("#liton_product_grid").html(response.products);
                $(".ltn__pagination").html(response.pagination);
            },
            complete: function () {
                $("#loading-spinner").hide();
                $("#liton_product_grid").show();
            },
            error: function (xhr) {
                alert("Có lỗi xảy ra với ajax fetchProducts!");
            },
        });
    }

    $(".category-filter").click(function () {
        $(".category-filter").removeClass("active");
        $(this).addClass("active");
        currentPage = 1;
        fetchProducts();
    });

    $("#sort-by").change(function () {
        currentPage = 1;
        fetchProducts();
    });

    $(".slider-range").slider({
        range: true,
        min: 0,
        max: 300000,
        values: [0, 300000],
        slide: function (event, ui) {
            $(".amount").val(ui.values[0] + " - " + ui.values[1] + "vnđ");
        },
        change: function (event, ui) {
            currentPage = 1;
            fetchProducts();
        },
    });
    $(".amount").val(
        $(".slider-range").slider("values", 0) +
            " - " +
            $(".slider-range").slider("values", 1) +
            " vnđ"
    );

    /***********************************************************
     * PAGE DETAIL PRODUCTS
     ***********************************************************/
    if (window.location.pathname !== "/cart") {
        $(document).on("click", ".qtybutton", function () {
            var $button = $(this);
            var $input = $button.siblings("input");
            var oldValue = parseInt($input.val());
            var maxStock = parseInt($input.data("max"));

            if ($button.hasClass("inc")) {
                if (oldValue < maxStock) {
                    $input.val(oldValue + 1);
                }
            } else {
                if (oldValue > 1) {
                    $input.val(oldValue - 1);
                }
            }
        });
    } else {
        $(document).on("click", ".qtybutton", function () {
            let $button = $(this);
            let $input = $button.siblings("input");
            let oldValue = parseInt($input.val());
            let maxStock = parseInt($input.data("max"));
            let productId = $input.data("id");
            let newValue = oldValue;

            if ($button.hasClass("inc") && oldValue < maxStock) {
                newValue = oldValue + 1;
            } else if ($button.hasClass("dec") && oldValue > 1) {
                newValue = oldValue - 1;
            }

            if (newValue != oldValue) {
                updateCart(productId, newValue, $input);
            }
        });
    }

    //Add to cart
    $(document).on("click", ".add-to-cart-btn", function (e) {
        e.preventDefault();

        let productId = $(this).data("id");
        let quantity = $(this)
            .closest("li")
            .prev()
            .find(".cart-plus-minus-box")
            .val();

        quantity = quantity ? quantity : 1;

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/cart/add",
            type: "POST",
            data: {
                product_id: productId,
                quantity: quantity,
            },
            success: function (response) {
                $("#add_to_cart_modal-" + productId).modal("show");
                $("#quick_view_modal-" + productId).modal("hide");
                $("#cart_count").text(response.cart_count);
            },
            error: function (xhr) {
                alert("Có lỗi xảy ra với ajax addToCart In Detail!");
            },
        });
    });

    /***********************************************************
     * MINI CARTS
     ***********************************************************/
    // Mini cart
    $(".mini-cart-icon").on("click", function (e) {
        $.ajax({
            url: "/mini-cart",
            type: "GET",
            success: function (response) {
                if (response.status) {
                    $("#ltn__utilize-cart-menu .ltn__utilize-menu-inner").html(
                        response.html
                    );
                    $("#ltn__utilize-cart-menu").addClass("ltn__utilize-open");
                } else {
                    toastr.error("Không thể tải giỏ hàng!");
                }
            },
        });
    });

    $(document).on("click", ".ltn__utilize-close", function () {
        $("#ltn__utilize-cart-menu").removeClass("ltn__utilize-open");
        $(".ltn__utilize-overlay").hide();
    });

    //Remove product from cart
    $(document).on("click", ".mini-cart-item-delete", function () {
        let productId = $(this).data("id");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/cart/remove",
            type: "POST",
            data: { product_id: productId },
            success: function (response) {
                if (response.status) {
                    $("#cart_count").text(response.cart_count);
                    $(".mini-cart-icon").click();
                }
            },
        });
    });

    /***********************************************************
     * PAGE CARTS
     ***********************************************************/

    //Hanlde update quantity product in Page Cart
    function updateCart(productId, quantity, $input) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/cart/update",
            type: "POST",
            data: {
                product_id: productId,
                quantity: quantity,
            },
            success: function (response) {
                $input.val(response.quantity);
                $input
                    .closest("tr")
                    .find(".cart-product-subtotal")
                    .text(response.total);
                $(".cart-total").text(response.total);
                $(".cart-grand-total").text(response.grandTotal);
            },
            error: function (xhr) {
                alert(xhr.responseJSON.error);
            },
        });
    }

    //Hanlde remove product in Page Cart
    $(".remove-from-cart").on("click", function (e) {
        let productId = $(this).data("id");
        let row = $(this).closest("tr");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/cart/remove-cart",
            type: "POST",
            data: {
                product_id: productId,
            },
            success: function (response) {
                row.remove();
                $(".cart-total").text(response.total);
                $(".cart-grand-total").text(response.grandTotal);
                if ($(".cart-product-remove").length === 0) {
                    location.reload();
                }
            },
            error: function (xhr) {
                alert(xhr.responseJSON.error);
            },
        });
    });

    /***********************************************************
     * PAGE CHECKOUT
     ***********************************************************/
    if (window.location.pathname === "/checkout") {
        $("#list_address").change(function () {
            var addressId = $(this).val();

            $.ajax({
                url: "/checkout/get-address",
                type: "GET",
                data: {
                    address_id: addressId,
                },
                success: function (response) {
                    if (response.success) {
                        $('input[name="ltn__name"]').val(response.data.full_name);
                        $('input[name="ltn__phone"]').val(response.data.phone);
                        $('input[name="ltn__address"]').val(response.data.address);
                        $('input[name="ltn__city"]').val(response.data.city);
                        $('input[name="address_id"]').val(response.data.id);
                    }
                },
                error: function (xhr) {
                    alert(xhr.responseJSON.error);
                },
            });
        });

        var summaryEl = $("#checkout-summary");
        var applyUrl = summaryEl.data("apply-url");
        var removeUrl = summaryEl.data("remove-url");
        var currencyFormatter = new Intl.NumberFormat("vi-VN");
        var totalPriceNumber = parseFloat(summaryEl.data("total")) || 0;

        function setSummaryData(key, value) {
            summaryEl.data(key, value);
        }

        function updateSummaryValues(data) {
            setSummaryData("subtotal", data.subtotal);
            setSummaryData("shipping-fee", data.shipping_fee);
            setSummaryData("discount", data.discount_amount);
            setSummaryData("total", data.total);

            $(".checkout-subtotal").text(currencyFormatter.format(Math.round(data.subtotal)));
            $("#checkout-shipping").text(currencyFormatter.format(Math.round(data.shipping_fee)));

            if (data.discount_amount > 0) {
                $("#coupon-row").show();
                $("#coupon-discount").text(currencyFormatter.format(Math.round(data.discount_amount)));
                $("#coupon-code-label").text(data.coupon_code);
                $("#removeCouponButton").show();
            } else {
                $("#coupon-row").hide();
                $("#coupon-discount").text("");
                $("#coupon-code-label").text("");
                $("#removeCouponButton").hide();
            }

            $(".totalPrice_Checkout").text(currencyFormatter.format(Math.round(data.total)) + " ₫");
            totalPriceNumber = data.total;
        }

        $("#applyCouponButton").on("click", function () {
            var code = $("#coupon_code").val().trim();

            if (!code) {
                toastr.error("Vui lòng nhập mã giảm giá.");
                return;
            }

            if (!applyUrl) {
                return;
            }

            $.ajax({
                url: applyUrl,
                type: "POST",
                data: {
                    coupon_code: code,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.status) {
                        updateSummaryValues(response.data);
                        $("#coupon_code").val(response.data.coupon_code);
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    var message = "Không thể áp dụng mã giảm giá.";
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                            if (firstKey) {
                                message = xhr.responseJSON.errors[firstKey][0];
                            }
                        }
                        if (xhr.responseJSON.status === false && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                    }
                    toastr.error(message);
                },
            });
        });

        $("#coupon_code").on("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                $("#applyCouponButton").click();
            }
        });

        $("#removeCouponButton").on("click", function () {
            if (!removeUrl) {
                return;
            }

            $.ajax({
                url: removeUrl,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.status) {
                        updateSummaryValues(response.data);
                        $("#coupon_code").val("");
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error("Không thể gỡ mã giảm giá.");
                },
            });
        });

        function togglePayment() {
            if ($("#payment_paypal").is(":checked")) {
                $("#order_button_cash").hide();
                $("#paypal-button-container").show();
            } else {
                $("#order_button_cash").show();
                $("#paypal-button-container").hide();
            }
        }
        togglePayment();

        $('input[name="payment_method"]').on("change", togglePayment);

        paypal
            .Buttons({
                createOrder: function (data, actions) {
                    return actions.order.create({
                        purchase_units: [
                            {
                                amount: {
                                    value: (totalPriceNumber / 25000).toFixed(2),
                                },
                            },
                        ],
                    });
                },
                onApprove: function (data, actions) {
                    return actions.order.capture().then(function (details) {
                        fetch("/checkout/paypal", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            body: JSON.stringify({
                                orderID: data.orderID,
                                payerID: data.payerID,
                                transactionID: details.id,
                                amount: details.purchase_units[0].amount.value,
                                address_id: $("#list_address").val(),
                            }),
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                if (data.success) {
                                    toastr.success("Thanh toán thành công!");
                                    window.location.href = "/account";
                                } else {
                                    toastr.error("Có lỗi xảy ra, vui lòng thử lại!");
                                }
                            })
                            .catch(function () {
                                toastr.error("Có lỗi xảy ra, vui lòng thử lại!");
                            });
                    });
                },
            })
            .render("#paypal-button-container");
    }

    /***********************************************************
     * HANDLE RATING PRODUCT
     ***********************************************************/
    if (window.location.pathname.startsWith("/product")) {
        let seletedRating = 0;

        //Handle hover star
        $(".rating-star").hover(
            function () {
                let value = $(this).data("value");
                highlightStars(value);
            },
            function () {
                highlightStars(seletedRating);
            }
        );
        $(".rating-star").click(function (e) {
            e.preventDefault();
            seletedRating = $(this).data("value");
            $("#rating-value").val(seletedRating);
            highlightStars(seletedRating);
        });

        function highlightStars(value) {
            $(".rating-star i").each(function () {
                let starValue = $(this).parent().data("value");
                if (starValue <= value) {
                    $(this).removeClass("far").addClass("fas"); //Show star
                } else {
                    $(this).removeClass("fas").addClass("far"); //Show star empty
                }
            });
        }

        //Handle submit rating with AJAX
        $("#review-form").submit(function (e) {
            e.preventDefault();

            let productId = $(this).data("product-id");
            let rating = $("#rating-value").val();
            let content = $("#review-content").val();

            if (rating == 0) {
                $("#review-content").html(
                    '<div class="alert alert-danger>Vui lòng chọn số sao!</div>'
                );
                return;
            }

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            $.ajax({
                url: "/review",
                type: "POST",
                data: {
                    product_id: productId,
                    rating: rating,
                    comment: content,
                },
                success: function (response) {
                    $("#review-content").val("");
                    highlightStars(0);
                    seletedRating = 0;
                    $(".ltn__comment-reply-area").hide();
                    toastr.success(response.message);

                    loadReviews(productId);
                },
                error: function (xhr) {
                    alert(xhr.responseJSON.error);
                },
            });
        });

        function loadReviews(productId) {
            $.ajax({
                url: "/review/" + productId,
                type: "GET",
                success: function (response) {
                    $(".ltn__comment-inner").html(response);
                },
            });
        }
    }

    /***********************************************************
     * HANDLE PAGE CONTACT
     ***********************************************************/
    $("#contact-form").on("submit", function (e) {
        let name = $('input[name="name"]').val();
        let email = $('input[name="email"]').val();
        let phone = $('input[name="phone"]').val();
        let message = $('textarea[name="message"]').val();
        let errorMessage = "";

        if (name.length < 3) {
            errorMessage += "Họ và tên phải có ít nhất 3 ký tự.<br>";
        }

        if (phone.length < 10 || phone.length > 11) {
            errorMessage += "Số điện thoại phải từ 10-11 số.<br>";
        }

        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            errorMessage += "Email không hợp lệ.<br>";
        }

        if (errorMessage !== "") {
            toastr.error(errorMessage, "Lỗi");
            e.preventDefault();
        }
    });

    /***********************************************************
     * HANDLE WISHLIST
     ***********************************************************/
    $(document).on("click", ".add-to-wishlist", function (e) {
        e.preventDefault();

        let productId = $(this).data("id");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/wishlist/add",
            type: "POST",
            data: {
                product_id: productId,
            },
            success: function (response) {
                if (response.status) {
                    $("#liton_wishlist_modal-" + productId).modal("show");
                }
            },
            error: function (xhr) {
                alert("Có lỗi xảy ra với ajax addToWishList.");
            },
        });
    });

    $(document).on("click", ".wistlist-product-remove", function (e) {
        e.preventDefault();

        let productId = $(this).data("id");
        let row = $(this).closest("tr");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/wishlist/remove",
            type: "POST",
            data: {
                product_id: productId,
            },
            success: function (response) {
                if (response.status) {
                    row.remove();
                    toastr.success("Đã xóa sản phẩm khỏi danh sách yêu thích!");
                }
            },
            error: function (xhr) {
                alert("Có lỗi xảy ra với ajax removeProductWishList.");
            },
        });
    });

    /***********************************************************
     * HANDLE SEARCH SPEECH RECOGNITION
     ***********************************************************/

    //Check brower support?
    if ("SpeechRecognition" in window || "webkitSpeechRecognition" in window) {
        var recognition = new (window.SpeechRecognition ||
            window.webkitSpeechRecognition)();
        recognition.lang = "vi-VN";
        recognition.continuous = true;
        recognition.interimResults = true;

        //
        var isRecognizing = false;

        $("#voice-search").on("click", function () {
            if (isRecognizing) {
                recognition.stop();
                $(this)
                    .removeClass("fa-microphone-slash")
                    .addClass("fa-microphone");
            } else {
                recognition.start();
                $(this)
                    .removeClass("fa-microphone")
                    .addClass("fa-microphone-slash");
            }
        });

        recognition.onstart = function () {
            console.log("Speech recognition started");
            isRecognizing = true;
            $("#voice-search")
                .removeClass("fa-microphone")
                .addClass("fa-microphone-slash");
        };

        recognition.onresult = function (event) {
            var transcript = event.results[0][0].transcript; //Get result recognition
            if (event.results[0].isFinal) {
                console.log(transcript);

                $('input[name="keyword"]').val(transcript);
            } else {
                $('input[name="keyword"]').val(transcript);
            }
        };

        recognition.onerror = function (event) {
            console.log("Speech recognition error", event.error);
            toastr.error(
                "Có lỗi xảy ra khi nhận diện giọng nói: " + event.error
            );
        };

        recognition.onend = function (event) {
            console.log("Speech recognition ened");
            $("#voice-search")
                .removeClass("fa-microphone-slash")
                .addClass("fa-microphone");
            isRecognizing = false;
        };
    } else {
        console.log("Speech recognition not supported in this brower.");
        toastr.error("Trình duyệt của bạn không hỗ trợ nhận diện giọng nói.");
    }
});
