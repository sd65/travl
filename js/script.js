$(document).ready(function () {

    if (typeof write != "undefined")
    {
        $('.randomButton').tooltip();

        $('.editable').editable({
            inlineMode: false,
            borderColor: "#333",
            buttons: ["formatBlock", "createLink", "insertImage", "sep", "bold", "italic", "underline", "sep", "fontSize", "color", "sep", "align", "insertOrderedList", "insertUnorderedList", "sep", "undo", "redo", "sep", "html"],
            language: "fr",
            minHeight: 300,
            placeholder: "Votre texte",
            imageUploadURL: 'index.php/upload',
            imagesLoadURL: 'index.php/listImages',
            imageDeleteURL: 'index.php/deleteImage',
            maxImageSize: 1024 * 1024 * 10,
            insertImageCallback: function (url) {
                loadImage(url);
            },
            replaceImageCallback: function (url) {
                loadImage(url);
            }
        });

        function loadImage(url) {
            $('.picture').attr('data-picture', url);
            if($('.picture').attr('pictureSet') != "true") {
                $('.picture').removeAttr('style').attr('pictureSet', 'true').css('background-image', "url('" + url + "')");
            }
        }

        $('.editableInline').editable({
            inlineMode: true,
            buttons: [],
            placeholder: "Écrivez quelque chose",
        });


        $(".send").click(function (event) {
            $.ajax({
                type: "POST",
                url: "index.php/submitWrite",
                data: {
                    title: $("#title").editable("getText"),
                    meta: $("#meta").editable("getText"),
                    content: $("#content").editable("getHTML"),
                    toPeople: $('#toPeople').val(),
                    picture: $('.picture').data('picture'),
                },
                success: function(people)
                {
                    window.location = 'index.php/?people=' + people;
                }
            });
        });

        $(".randomButton").click(function (event) {
            $.ajax({
                type: "GET",
                url: "index.php/getRandomImg",
                success: function(url)
                {
                    $('.picture').removeAttr('style').attr('pictureSet', 'false').css('background-image', "url('" + url + "')");
                }
            });
        });

        $(".enlargePicture").click(function (event) {
            $('.picture').toggleClass('background');
        });

    }

    $("#sidebar-wrapper").hover(
         function() {
            $( this ).addClass( "active" );
        }, function() {
            $( this ).removeClass( "active" );
        }
    );

    $(document).swipe( {
        swipeLeft:function() {
            $("#sidebar-wrapper").removeClass( "active" );
        },
        swipeRight:function() {
            $("#sidebar-wrapper").addClass( "active" );
        },
    });

    setTimeout(function(){
        $("#sidebar-wrapper").removeClass("active");
    },200);


});