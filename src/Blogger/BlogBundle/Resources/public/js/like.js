function setLikeOrDislike(like) {
    var url = (like) ? 'setLike/like' : 'setDislike/dislike';

    $.ajax({
        type: 'GET',
        url: url,
        success: function (data) {
            if (data) {
                $('#count-like').text(data.likes);
                $('#count-dislike').text(data.dislikes);
            }
        },
        error: function (jqXHR) {
            if (403 == jqXHR.status) {
            }
        }
    });
}

$('body').on('click', '#like', function () {
    setLikeOrDislike(true);
});

$('body').on('click', '#dislike', function () {
    setLikeOrDislike();
});