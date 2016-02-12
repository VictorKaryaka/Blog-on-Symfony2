function setImageTitle(image) {
    var src = image.attr('src');
    var name = src.substr(src.lastIndexOf('/') + 1);
    blockUI();

    $.ajax({
        type: 'GET',
        url: 'setTitleImage/' + name,
        success: function (data) {
            if (data.notice == 'success') {
                $.unblockUI();
                image.notify("Изображение установлено на заголовок!", "success");
            }
        }
    }).always(function () {
        $.unblockUI();
    });
}

function deleteImage(image) {
    var src = image.attr('src');
    var name = src.substr(src.lastIndexOf('/') + 1);
    blockUI();
    $.ajax({
        type: 'GET',
        url: 'deleteImage/' + name,
        success: function (data) {
            if (data.notice == 'success') {
                $.unblockUI();
                image.remove();
            }
        }
    }).always(function () {
        $.unblockUI();
    });
}

function editBlog(submitForm, files) {
    var url = $('#update-blog').attr('action');
    var formData = new FormData();
    var submitParams = submitForm.serializeArray();

    blockUI();

    $.each(files, function (key, value) {
        formData.append(key, value);
    });

    $.each(submitParams, function (key, value) {
        formData.append(value.name, value.value);
    });

    $.ajax({
        type: 'POST',
        url: url,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (data) {
            if (data.notice == 'success') {
                $.unblockUI();
                $('#blog-title').text(data.title);
                $('#blog-content').text(data.blog);
                $('#blog-tag').text(data.tags);
                $('.blog').find('.large').remove();
                $.each(data.images, function (key, value) {
                    $('.blog').append('<img class="large"' +
                        'src="/images/' + value + '"' +
                        'alt="image not found">');
                });
            }
        }
    }).always(function () {
        $.unblockUI();
    });
}

function blockUI() {
    $.blockUI({
        css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        }
    });
}

$(document).ready(function () {
    var setTitleImageMode = false;
    var deleteImageMode = false;
    var files;

    function buttonAction(mode, image, notice) {
        if (mode) {
            image.css('opacity', 0.7);
            $('.large').attr('title', notice);
        } else {
            image.css('opacity', 1);
            $('.large').removeAttr('title');
        }
    }

    if ($('.large').length == 0) {
        $('#but-image').hide();
        $('#but-image-delete').hide();
    } else {
        $('#but-image').show();
        $('#but-image-delete').show();
    }

    $("#blogEditType_uploadedFiles").on('change', function () {
        files = event.target.files;
    });

    if ($('.large').length == 0) {
        $('#but-image').hide();
        $('#but-image-delete').hide();
    } else {
        $('#but-image').show();
        $('#but-image-delete').show();
    }

    $('#but-edit').click(function () {
        if ($('#update-blog').is(':hidden')) {
            $('#update-blog').show('slow');
            $('#blogEditType_title').val($('#blog-title').text());
            $('#blogEditType_blog').val($('#blog-content').text());
            $('#blogEditType_tags').val($('#blog-tag').text().replace('Tags: ', ''));
        } else {
            $('#update-blog').hide('slow');
        }
    });

    $('#but-image').click(function () {
        setTitleImageMode = (!setTitleImageMode) ? true : false;
        buttonAction(setTitleImageMode, $(this), 'Кликните, чтобы выбрать изображение для заголовка');
    });

    $('#but-image-delete').click(function () {
        deleteImageMode = (!deleteImageMode) ? true : false;
        buttonAction(deleteImageMode, $(this), 'Кликните, чтобы удалить изображение');
    });

    $('body').on('mouseover', '.large', function () {
        if (setTitleImageMode || deleteImageMode) {
            $(this).attr('id', 'image-action');
        }
    });

    $('body').on('mouseout', '.large', function () {
        $(this).removeAttr('id');
    });

    $('body').on('click', '.large', function () {
        if (setTitleImageMode) {
            setImageTitle($(this));
        }

        if (deleteImageMode && confirm('Are you sure?')) {
            deleteImage($(this));
        }
    });

    $('#update-blog').submit(function () {
        event.preventDefault();
        $('#update-blog').hide('slow');
        editBlog($(this), files);
    });
});
