function setImageTitle(image)
{
    var src = image.attr('src');
    var name = src.substr(src.lastIndexOf('/') + 1);

    $.ajax({
        type: 'GET',
        url: 'setTitleImage/' + name,
        success: function (data) {
            image.notify("Изображение установлено на заголовок!", "success");
        }
    });
}

function deleteImage(image) {
    var src = image.attr('src');
    var name = src.substr(src.lastIndexOf('/') + 1);

    $.ajax({
        type: 'GET',
        url: 'deleteImage/' + name,
        success: function (data) {
            console.log(data.notice);
            if (data.notice == 'success') location.reload();
        }
    });
}

$(document).ready(function () {
    var setTitleImageMode = false;
    var deleteImageMode = false;

    function buttonAction(mode, image, notice) {
        if (mode) {
            image.css('opacity', 0.7);
            $('.large').attr('title', notice);
        } else {
            image.css('opacity', 1);
            $('.large').removeAttr('title');
        }
    }

    $('#but-edit').click(function () {
        if ($('#update-blog').is(':hidden')) {
            $('#update-blog').show('slow');
            $('#blogEditType_title').val($('#blog-content').text());
            $('#blogEditType_blog').val($('#blog-title').text());
            $('#blogEditType_tags').val($('#blog-tag').text().replace('Tags: ', ''));
        } else {
            $('#update-blog').hide('slow');
        }
    });

    $('#but-image').click(function () {
        (!setTitleImageMode) ? setTitleImageMode = true : setTitleImageMode = false;
        buttonAction(setTitleImageMode, $(this), 'Кликните, чтобы выбрать изображение для заголовка');
    });

    $('#but-image-delete').click(function () {
        (!deleteImageMode) ? deleteImageMode = true : deleteImageMode = false;
        buttonAction(deleteImageMode, $(this), 'Кликните, чтобы удалить изображение');
    });

    $('.large').mouseover(function () {
        if (setTitleImageMode || deleteImageMode) {
            $(this).attr('id', 'image-action');
        }
    });

    $('.large').mouseout(function () {
        $(this).removeAttr('id');
    })

    $('.large').click(function () {
        if (setTitleImageMode) {
            setImageTitle($(this))
        }

        if (deleteImageMode) {
            deleteImage($(this));
        }
    });
});
