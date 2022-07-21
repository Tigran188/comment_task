$(document).ready(function () {
    $(".comment_new").click(function () {
        $(".comments").removeClass("d-none")
        $(".form_comment").addClass("d-none")
        $(".comment_new").addClass("active_text")
        $(".comment_wirte").removeClass("active_text")
    })
    $(".comment_wirte").click(function () {
        $(".comments").addClass("d-none")
        $(".comment_wirte").addClass("active_text")
        $(".comment_new").removeClass("active_text")
        $(".form_comment").removeClass("d-none")
    })
    var star_id;
    $(".selected_star").click(function (){
          star_id = Number($(this).attr("data-id"))

        if (star_id > 0){

            for (let i = star_id; i > 0; i-- ){
                $(this).attr("src", "image/star_old.svg")
                // cc$(this).prev().attr("src", "image/star_old.svg")
                var arr = Array.from($(".selected_star"))
                $(arr[i-1]).attr("src", "image/star_old.svg")
            }
        }
    })




    $(".submit").click(function (e) {
        e.preventDefault();

        var comment_text = $("#comment_text").val();
        var title = $("#title_comment").val();
        var element_id  = $("#id_element").val();

        $.ajax({
            url: '',
            method: 'POST',
            dataType: 'html',
            data: {flag: '1', star_id: star_id, comment_text: comment_text,  title: title, element_id: element_id },
            success: function (data) {
                if (data == "ok"){
                    $("#title_comment").val(" ");
                    $("#comment_text").val(" ");
                    $(".selected_star").attr("src" , "image/star.svg")

                    $(".appand_div").append('<h3>'+ title + '</h3>');
                    $(".appand_div").append('<p>'+ comment_text + '</p>');

                }

            }
        })
    })




})
