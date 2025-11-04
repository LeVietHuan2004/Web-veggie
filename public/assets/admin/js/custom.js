$(document).ready(function () {
    /***********************************************************
     * MANAGEMENT USERS
     ***********************************************************/
    $(document).on("click", ".upgradeStaff", function (e) {
        let button = $(this);
        let userId = button.data("userid");
        let targetRole = button.data("role") || 'staff';

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            type: "POST",
            url: "user/upgrade",
            data: {
                user_id: userId,
                role: targetRole,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    button
                        .closest(".profile_view")
                        .find(".brief i")
                        .text(targetRole.toUpperCase());
                    button
                        .closest(".profile_view")
                        .find(".changeStatus")
                        .hide();
                    button.hide();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    });

    $(document).on("click", ".changeStatus", function (e) {
        let button = $(this);
        let userId = button.data("userid");
        let status = button.data("status");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            type: "POST",
            url: "user/updateStatus",
            data: {
                user_id: userId,
                status: status,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    status == "banned"
                        ? button.text("Đã chặn")
                        : button.text("Đã xóa");
                    button.addClass("disabled").prop("disabled", true);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    });

    $(".btn_reset").on("click", function () {
        let form = $(this).closest("form");
        form.trigger("reset");
        form.find('input[type="file"]').val("");
        form.find("#image-preview").html("");
        form.find("#image-preview").attr("src", "");

        form.find("#image-preview-container").html("");
    });

    /***********************************************************
     * MANAGEMENT CATEGORIES
     ***********************************************************/
    $("#category-image").change(function () {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $("#image-preview").attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            $("#image-preview").attr("src", "");
        }
    });

    $(".category-image").change(function () {
        let file = this.files[0];
        let categoryId = $(this).data("id");
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $(".image-preview").each(function () {
                    if (
                        $(this).closest(".modal").attr("id") ===
                        "modalUpdate-" + categoryId
                    ) {
                        $(this).attr("src", e.target.result);
                    }
                });
            };
            reader.readAsDataURL(file);
        } else {
            $("#image-preview").attr("src", "");
        }
    });

    //UPDATE CATEGORY
    $(document).on("click", ".btn-update-submit-category", function (e) {
        e.preventDefault();
        let button = $(this);
        let categoryId = button.data("id");
        let form = button.closest(".modal").find("form");
        let formData = new FormData(form[0]);

        formData.append("category_id", categoryId);
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "categories/update",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                button.prop("disabled", true);
                button.text("Đang cập nhật...");
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    let categoryId = product.id;

                    //Regenerate new HTML for updated row
                    let newRow = `
                        <tr id="category-row-${categoryId}">
                            <td>
                                <img src="${response.data.image}" alt="${response.data.name}" width="80">
                            </td>
                            <td>${response.data.name}</td>
                            <td>${response.data.slug}</td>
                            <td>${response.data.description}</td>
                            <td>
                                <a class="btn btn-app btn-update-category" data-toggle="modal"
                                    data-target="#modalUpdate-${categoryId}">
                                    <i class="fa fa-edit"></i>Chỉnh sửa
                                </a>
                                
                            </td>
                            <td>
                                <a class="btn btn-app btn-delete-category" data-id="${categoryId}">
                                    <i class="fa fa-trash"></i>Xóa
                                </a>
                            </td>
                        </tr>`;

                    //Replace old row with new row
                    $("#category-row-" + categoryId).replaceWith(newRow);

                    $("#modalUpdate-" + categoryId).modal("hide");
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
            complete: function () {
                button.prop("disabled", false);
                button.text("Chỉnh sửa");
            },
        });
    });

    //DELETE CATEGORY
    $(document).on("click", ".btn-delete-category", function (e) {
        e.preventDefault();
        let button = $(this);
        let categoryId = button.data("id");
        let row = button.closest("tr");

        if (confirm("Bạn có chắc chắn muốn xóa danh mục này?")) {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            $.ajax({
                url: "categories/delete",
                type: "POST",
                data: {
                    category_id: categoryId,
                },
                success: function (response) {
                    if (response.status) {
                        toastr.success(response.message);
                        row.fadeOut(300, function () {
                            $(this).remove();
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert("Cố lỗi xảy ra ! Vui lòng thử lại. " + error);
                },
            });
        }
    });

    /***********************************************************
     * MANAGEMENT PRODUCTS
     ***********************************************************/
    $("#product-images").change(function (e) {
        let files = e.target.files;
        console.log(files);
        let previewContainer = $("#image-preview-container");
        previewContainer.empty();

        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        let img = $("<img>")
                            .attr("src", e.target.result)
                            .addClass("image-preview");
                        img.css({
                            "max-width": "150px",
                            "max-height": "150px",
                            margin: "5px",
                            "border-radius": "5px",
                        });
                        previewContainer.append(img);
                    };
                    reader.readAsDataURL(file);
                }
            }
        } else {
            previewContainer.html("");
        }
    });

    $(".product-images").change(function (e) {
        let files = e.target.files;
        let productId = $(this).data("id");
        let previewContainer = $("#image-preview-container-" + productId);
        previewContainer.empty();

        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        let img = $("<img>")
                            .attr("src", e.target.result)
                            .addClass("image-preview");
                        img.css({
                            "max-width": "150px",
                            "max-height": "150px",
                            margin: "5px",
                            "border-radius": "5px",
                        });
                        previewContainer.append(img);
                    };
                    reader.readAsDataURL(file);
                }
            }
        } else {
            previewContainer.html("");
        }
    });

    //UPDATE PRODUCT
    $(document).on("click", ".btn-update-submit-product", function (e) {
        e.preventDefault();
        let button = $(this);
        let productId = button.data("id");
        let form = button.closest(".modal").find("form");
        let formData = new FormData(form[0]);

        formData.append("id", productId);
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "product/update",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                button.prop("disabled", true);
                button.text("Đang cập nhật...");
            },
            success: function (response) {
                if (response.status) {
                    let product = response.data;
                    let productId = product.id;

                    let imageSrc =
                        product.images.length > 0
                            ? product.images[0]
                            : "storage/products/default-product.png";

                    //Regenerate new HTML for updated row
                    let newRow = `
                        <tr id="product-row-${productId}">
                            <td>
                                <img src="${imageSrc}" alt="${
                        product.name
                    }" class="image-product" width="80">
                            </td>
                            <td>${product.name}</td>
                            <td>${product.category_name}</td>
                            <td>${product.slug}</td>
                            <td>${product.description}</td>
                            <td>${product.stock}</td>
                            <td>${new Intl.NumberFormat("vi-VN").format(
                                product.price
                            )} VNĐ</td>
                            <td>${product.unit}</td>
                            <td>${product.status}</td>
                            <td>
                                <a class="btn btn-app btn-update-product" data-toggle="modal"
                                    data-target="#modalUpdate-${productId}">
                                    <i class="fa fa-edit"></i>Chỉnh sửa
                                </a>    
                            </td>
                            <td>
                                <a class="btn btn-app btn-delete-product" data-id="${productId}">
                                    <i class="fa fa-trash"></i>Xóa
                                </a>
                            </td>
                        </tr>`;
                    //Replace old row with new row
                    $("#product-row-" + productId).replaceWith(newRow);
                    toastr.success(response.message);
                    $("#modalUpdate-" + productId).modal("hide");
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
            complete: function () {
                button.prop("disabled", false);
                button.text("Chỉnh sửa");
            },
        });
    });

    //DELETE PRODUCT
    $(document).on("click", ".btn-delete-product", function (e) {
        e.preventDefault();
        let button = $(this);
        let productId = button.data("id");
        let row = button.closest("tr");

        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            $.ajax({
                url: "product/delete",
                type: "POST",
                data: {
                    id: productId,
                },
                success: function (response) {
                    if (response.status) {
                        toastr.success(response.message);
                        row.fadeOut(300, function () {
                            $(this).remove();
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert("Cố lỗi xảy ra ! Vui lòng thử lại. " + error);
                },
            });
        }
    });

    /***********************************************************
     * MANAGEMENT ORDERS
     ***********************************************************/
    $(document).on("click", ".confirm-order", function (e) {
        e.preventDefault();
        let button = $(this);
        let orderId = button.data("id");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            type: "POST",
            url: "order/confirm",
            data: {
                id: orderId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    button
                        .closest("tr")
                        .find(".order-status")
                        .html(
                            '<span class="custom-badge badge badge-primary">Đang xử lý</span>'
                        );
                    button.hide();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    });

    //Send mail to customer
    $(document).on("click", ".send-invoice-mail", function (e) {
        e.preventDefault();
        let button = $(this);
        let orderId = button.data("id");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            type: "POST",
            url: "send-invoice",
            data: {
                id: orderId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    button.remove();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    });

    //Cancel order
    $(document).on("click", ".cancel-order", function (e) {

        e.preventDefault();
        let button = $(this);
        let orderId = button.data("id");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            type: "POST",
            url: "cancel-order",
            data: {
                id: orderId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    button.remove();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    });

    $(document).on("click", ".submit-assign-delivery", function (e) {
        e.preventDefault();
        let button = $(this);
        let orderId = button.data("order-id");
        let modal = button.closest(".modal");
        let form = modal.find(".assign-delivery-form");
        let deliveryStaffId = form.find("select[name='delivery_staff_id']").val();
        let note = form.find("textarea[name='note']").val();
        console.log(1212121);
        

        if (!deliveryStaffId) {
            toastr.error('Vui lòng chọn nhân viên giao hàng.');
            return;
        }

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            type: "POST",
            url: "order/ready",
            data: {
                order_id: orderId,
                delivery_staff_id: deliveryStaffId,
                note: note,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    window.location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    });

    $(document).on("click", ".start-delivery", function (e) {
        e.preventDefault();
        let orderId = $(this).data("id");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            type: "POST",
            url: "deliveries/start",
            data: {
                order_id: orderId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    window.location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    });

    $(document).on("click", ".complete-delivery", function (e) {
        e.preventDefault();
        let orderId = $(this).data("id");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            type: "POST",
            url: "deliveries/complete",
            data: {
                order_id: orderId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    window.location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    });

    /***********************************************************
     * MANAGEMENT CONTACTS
     ***********************************************************/

    if ($("#editor-contact").length) {
        CKEDITOR.replace("editor-contact");
    }

    $(document).on("click", ".contact-item", function (e) {
        //Get contact data from clicked item
        let contactName = $(this).data("name");
        let contactEmail = $(this).data("email");
        let contactMessage = $(this).data("message");
        let contactId = $(this).data("id");
        let isReplied = $(this).attr("data-is_replied")

        $(".mail_view .inbox-body .sender-info strong").text(contactName);
        $(".mail_view .inbox-body .sender-info span").text(
            "(" + contactEmail + ")"
        );
        $(".mail_view .view-mail p").text(contactMessage);

        console.log(contactId, isReplied);
        $(".mail_view").show();

        if (isReplied != 0) {
            $("#compose").hide();
        } else {
            //Add atrribute data-email to button reply
            $(".send-reply-contact").attr("data-email", contactEmail);
            $(".send-reply-contact").attr("data-id", contactId);
            $("#compose").show();
        }
    });

    $(document).on("click", ".send-reply-contact", function (e) {
        e.preventDefault();
        let button = $(this);
        let email = button.data("email");
        let contactId = button.data("id");
        let message = CKEDITOR.instances["editor-contact"].getData();

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            type: "POST",
            url: "contact/reply",
            data: {
                email: email,
                message: message,
                contact_id: contactId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    $(".mail_view").hide();
                    $("#compose").hide();
                    CKEDITOR.instances["editor-contact"].setData("");
                    $("#editor-contact").empty();

                    let contactItem = $('.contact-item[data-id="' + contactId + '"]');
                    contactItem.attr("data-is_replied", "1");

                    contactItem.find("i.fa-circle").css("color", "green");

                    $(".compose").slideToggle();
                    button
                        .removeAttr("data-email")
                        .removeAttr("data-contactid");
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    });

    /***********************************************************
     * MANAGEMENT PROFILE
     ***********************************************************/
    $(".form-change-pass").on("click", function (e) {
        e.preventDefault();
        $("#change-password").toggle();
        if ($("#change-password").is(":visible")) {
            $(this).text("Đóng");
        } else {
            $(this).text("Đổi mật khẩu");
        }
    });

    $('.update-avatar').on('click', function (e) {
        e.preventDefault();
        $('#avatar').trigger('click');
    });

    $('#avatar').on('change', function (e) {
        let file = e.target.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#avatar-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);

            //Create formData to send image
            let formData = new FormData();
            formData.append("type", "avatar");
            formData.append("avatar", file);

            updateProfile(formData);
        } else {
            $('#avatar-preview').attr('src', '');
        }
    });

    $("#update-profile").submit(function (e){
        let valid = true;
        let name = $('#name').val().trim();
        let phone = $('#phone').val().trim();
        let address = $('#address').val().trim();
        e.preventDefault();

        if(name.length < 3)
        {
            toastr.error("Họ và tên phải có ít nhất 3 ký tự.");
            valid = false;
        }

        let phoneRegex = /^0\d{9}$/;
        if(!phoneRegex.test(phone))
        {
            toastr.error(
                "Số điện thoại không hợp lệ. Phải có 10 số và bắt đầu bằng 0."
            );
            valid = false;
        }

        if(address === "")
        {
            toastr.error("Địa chỉ không được để trống.");
            valid = false;
        }

        if(valid)
        {
            let formData = new FormData();
            formData.append('type', "profile");
            formData.append('name', name);
            formData.append('phone', phone);
            formData.append('address', address);
            
            updateProfile(formData);
        }
    });

    $("#change-password").submit(function (e){
        let valid = true;
        let current_password = $('#current_password').val().trim();
        let new_password = $('#new_password').val().trim();
        let confirm_password = $('#confirm_password').val().trim();
        e.preventDefault();

        if(current_password === "")
        {
            toastr.error("Bạn cần nhập mật khẩu hiện tại.");
            valid = false;
        }

        if (new_password.length < 6) {
            toastr.error("Mật khẩu mới phải có ít nhất 6 ký tự.");
            valid = false;
        }

        if (new_password !== confirm_password) {
            toastr.error("Mật khẩu xác nhận không khớp.");
            valid = false;
        }

        if(valid)
        {
            let formData = new FormData();
            formData.append("type", "password");
            formData.append("current_password", current_password);
            formData.append("new_password", new_password);
            formData.append("confirm_password", confirm_password);
            
            updateProfile(formData);
        }
    });

    function updateProfile(formData)
    {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "profile/update",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    
                    if(formData.get("type") === "profile"){                        
                        $("#user-name").text(formData.get("name"));
                        $("#user-address").text(formData.get("address"));
                        $("#user-phone").text(formData.get("phone"));
                    }

                    if(formData.get("type") === "password"){
                        $("#change-password")[0].reset();
                    }

                    if(formData.get("type") === "avatar"){
                        $("#avatar-preview").attr("src", response.avatar_url);
                    }

                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    }

    /***********************************************************
     * MANAGEMENT NOTIFICATIONS
     ***********************************************************/
    $(document).on('click','.notification-item', function(e){
        let noti_id = $(this).data("id");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "notification/update",
            type: "POST",
            dataType: "json",
            data: {id : noti_id},
            success: function (response) {
            },
            error: function (xhr, status, error) {
                alert("Đã xảy ra lỗi với thông báo. Vui lòng thử lại.");
            },
        });
    })

});
