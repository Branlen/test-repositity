(function ($) {
  $(document).ready(function () {
    var ajaxQueue = $({});
    var processing = true;

    //Ajax queue requests. Used for synchronous transfer of pictures for processing.
    $.ajaxQueue = function (ajaxOpts) {
      // Hold the original complete function
      var oldComplete = ajaxOpts.complete;

      // Queue our ajax request
      ajaxQueue.queue(function (next) {
        // Create a complete callback to invoke the next event in the queue
        ajaxOpts.complete = function () {
          // Invoke the original complete if it was there
          if (oldComplete) {
            oldComplete.apply(this, arguments);
          }

          // Run the next query in the queue
          if (processing) {
            next();
          } else {
            processing = true;
          }
        };

        // Run the query
        if (processing) {
          $.ajax(ajaxOpts);
        }
      });
    };

    //We add to processing a new picture with data to which post it belongs. The queue will be synchronous.
    function processQueue(post, image, thumb, gallery, currentProcessing, allProcessing, last, remove_bg_id) {
      console.log($('input[name="Pixcut_RemoveBG_Include_Processed"]:checked').val());
      $.ajaxQueue({
        type: "post",
        dataType: "json",
        url: ajaxurl,
        data: {
          action: "Pixcut_Remove_BG_processing",
          process: "processing_queue",
          Pixcut_RemoveBG_NextPost: post,
          Pixcut_RemoveBG_NextImage: image,
          Pixcut_RemoveBG_NextImageThumb: thumb,
          Pixcut_RemoveBG_NextImageGallery: gallery,
          Pixcut_RemoveBG_CountProcessImage: currentProcessing,
          Pixcut_RemoveBG_AllCountImage: allProcessing,
          Pixcut_RemoveBG_LastImage: last,
          Pixcut_RemoveBG_ApiKey: $('input[name="Pixcut_RemoveBG_ApiKey"]').val(),
          Pixcut_RemoveBG_products: $('input[name="Pixcut_RemoveBG_products"]:checked').val(),
          Pixcut_RemoveBG_products_IDs: $('input[name="Pixcut_RemoveBG_products_IDs"]').val(),
          Pixcut_RemoveBG_thumbnail: $('input[name="Pixcut_RemoveBG_thumbnail"]:checked').val(),
          Pixcut_RemoveBG_gallery: $('input[name="Pixcut_RemoveBG_gallery"]:checked').val(),
          Pixcut_RemoveBG_Include_Processed: $('input[name="Pixcut_RemoveBG_Include_Processed"]:checked').val() || false,
          Pixcut_RemoveBG_Background: $('input[name="Pixcut_RemoveBG_Background"]:checked').val(),
          Pixcut_RemoveBG_Background_Color: $('input[name="Pixcut_RemoveBG_Background_Color"]').val(),
          //   Pixcut_RemoveBG_Background_fit_fill: $('input[name="RemoveBG_Background_fit_fill"]:checked').val(),
          Pixcut_RemoveBG_ID: remove_bg_id,
          schk: $("input#schk").val(),
          _nonce: $("#_wpnonce").val(),
        },
        success: function (data) {
          if (data.hasErrors == true) {
            if (data.error_msg === "Credit not enough") {
              showModel("Credit not enough", null, function () {
                window.open("https://pixcut.wondershare.com/pricing.html", "_blank");
              });
            } else {
              $(".pixcut_remove_bg").hide();
              $("#status_restore_e").show();
              $("#status_restore_e p").html(data.error_msg + " (" + $(".pixcut_remove_bg-log").html() + ")");
              $("html, body").animate({ scrollTop: 0 }, "slow");

              $("#loader").hide();
              $("p.submit").show();
            }

            ajaxQueue = $({});
            processing = false;
          } else {
            if (currentProcessing > 0) {
              $(".pixcut-button-click").removeClass("d-none");
              $(".block-count span").html(currentProcessing);
            }
            // $(".RemoveBG_Background_img").attr("src", "").css("display", "none");
            if (data.success_msg != "") {
              if (processing !== false) {
                $(".pixcut_remove_bg-log-live")
                  .show()
                  .html(data.success_msg + " (" + $(".pixcut_remove_bg-log").html() + ")");
                $(".pixcut_remove_bg-process-stop").show().attr("data-id", remove_bg_id);
                $("html,body").animate(
                  {
                    scrollTop: $(".pixcut_remove_bg-log-live").offset().top,
                  },
                  "slow"
                );
              }
            }
            if (last) {
              $(".pixcut_remove_bg").hide();
              $(".pixcut_remove_bg#status_s").show();
              $(".pixcut_remove_bg#status_s p").html(data.success_msg + " (" + $(".pixcut_remove_bg-log").html() + ")");
              $("html, body").animate({ scrollTop: 0 }, "slow");
              $(".pixcut_remove_bg-log-live").hide();
              $(".pixcut_remove_bg-process-stop").hide().attr("data-id", 0);
              $("#loader").hide();
              $("p.submit").show();
              $(".block-count").show();
            }
          }
          return true;
        },
      });
      return true;
    }

    // $('input[name="Pixcut_RemoveBG_Background_Color"]').wpColorPicker();
    if ($('input[name="Pixcut_RemoveBG_Background"]:checked').val() == "color") $("#background_color").show();
    else $("#background_color").hide();

    $('input[name="Pixcut_RemoveBG_Background"]').on("click", function () {
      if ($('input[name="Pixcut_RemoveBG_Background"]:checked').val() == "color") $("#background_color").show();
      else $("#background_color").hide();
    });

    // start remove clicker
    $("form#Pixcut_RemoveBG_Form input.button-primary").on("click", function (e) {
      e.preventDefault();
      $("#previewresult").hide();
      if (this.id == "startpreview") return false;
      $(".pixcut_remove_bg").hide();
      var btn = $(this),
        process,
        start_p = false,
        start_nb = false,
        start_r = true;
      if (btn.hasClass("startRemove")) {
        process = "start_queue"; //'new';
        $(".pixcut_remove_bg#status_w").show();
        $("#process_status").val("w");
      }
      if (btn.hasClass("saveSetting")) {
        process = "save";
      }
      if ($('input[name="Pixcut_RemoveBG_thumbnail"]:checked').length || $('input[name="Pixcut_RemoveBG_gallery"]:checked').length) {
        start_p = true;
      }
      if ($('input[name="Pixcut_RemoveBG_Background"]:checked').val() == "color" && $('input[name="Pixcut_RemoveBG_Background_Color"]').val() != "")
        start_nb = true;
      // if($('input[name="Pixcut_RemoveBG_Background"]:checked').val() == 'image' && $(".Pixcut_RemoveBG_Background_Image")[0].files[0] != '')
      //     start_nb = true;
      if ($('input[name="Pixcut_RemoveBG_Background"]:checked').val() == "transparent ") start_nb = true;

      if (start_r) {
        if (start_p && start_nb) {
          $("#loader").show();
          $("p.submit").hide();

          // var file_data = $(".RemoveBG_Background_Image")[0].files[0];
          var form_data = new FormData();
          // form_data.append("Pixcut_RemoveBG_file", file_data);
          form_data.append("action", "Pixcut_Remove_BG_processing");
          form_data.append("process", process);
          form_data.append("Pixcut_RemoveBG_CountProcessImage", 0);
          form_data.append("Pixcut_RemoveBG_AllCountImage", 0);
          form_data.append("Pixcut_RemoveBG_ApiKey", $('input[name="Pixcut_RemoveBG_ApiKey"]').val());
          form_data.append("Pixcut_RemoveBG_products", $('input[name="Pixcut_RemoveBG_products"]:checked').val());
          form_data.append("Pixcut_RemoveBG_products_IDs", $('input[name="Pixcut_RemoveBG_products_IDs"]').val());
          form_data.append("Pixcut_RemoveBG_thumbnail", $('input[name="Pixcut_RemoveBG_thumbnail"]:checked').val());
          form_data.append("Pixcut_RemoveBG_gallery", $('input[name="Pixcut_RemoveBG_gallery"]:checked').val());
          form_data.append("Pixcut_RemoveBG_Include_Processed", $('input[name="Pixcut_RemoveBG_Include_Processed"]:checked').val());
          form_data.append("Pixcut_RemoveBG_Background", $('input[name="Pixcut_RemoveBG_Background"]:checked').val());
          form_data.append("Pixcut_RemoveBG_Background_Color", $('input[name="Pixcut_RemoveBG_Background_Color"]').val());
          form_data.append("_nonce", $("#_wpnonce").val());
          form_data.append("schk", $("input#schk").val());

          $.ajax({
            type: "post",
            dataType: "json",
            cache: false,
            contentType: false,
            processData: false,
            url: ajaxurl,
            data: form_data,
            success: function (data) {
              if (process == "start_queue") {
                // if(data.background_image != ""){
                //     $('.RemoveBG_Background_img').attr('src', data.background_image).css('display', 'block');
                // }
                var arrayPost = data.data;
                if (arrayPost && arrayPost !== "") {
                  var arrayPostJson = $.parseJSON(arrayPost);
                  var countGenerateImage = $(arrayPostJson).length;
                  if (countGenerateImage > 0) {
                    $(arrayPostJson).each(function (item, res) {
                      var iteration = item + 1;
                      processQueue(
                        res.id,
                        res.image,
                        res.thumb,
                        res.gallery,
                        iteration,
                        countGenerateImage,
                        iteration == countGenerateImage ? 1 : 0,
                        data.remove_bg
                      );
                    });
                  } else {
                    ajaxQueue = $({});
                    processing = false;
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    $("#status_restore_e").show();
                    $("#status_restore_e p").text($("#alert-text-no-images").val());
                    $("#loader").hide();
                    $("p.submit").show();
                  }
                }
                if (data.hasErrors == true) {
                  ajaxQueue = $({});
                  processing = false;
                  $("html, body").animate({ scrollTop: 0 }, "slow");
                  $("#status_restore_e").show();
                  $("#status_restore_e p").text(data.error_msg);
                  $("#loader").hide();
                  $("p.submit").show();
                }
              }
              if (process == "save") {
                if ($('input[name="Pixcut_RemoveBG_ApiKey"]').val() != "") {
                  $("#appkeyWarning").hide();
                } else {
                  $("#appkeyWarning").show();
                }
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $("#status_s").show();
                $("#loader").hide();
                $("p.submit").show();
              }
            },
            error: function (jqXHR, textStatus, errorThrown) {
              $("html, body").animate({ scrollTop: 0 }, "slow");
              $("#status_restore_e").show();
              $("#status_restore_e p").text(textStatus + " " + errorThrown);
              $("#loader").hide();
              $("p.submit").show();
            },
          });
        } else {
          showModel($("#alert-text").val()); //alert-text
        }
      }
    });

    $(".pixcut-button-click").on("click", function (e) {
      $(".pixcut_remove_bg.notice").hide();
    });

    $(".pixcut_remove_bg-process-stop").on("click", function (e) {
      e.preventDefault();
      ajaxQueue = $({});
      processing = false;
      $("#loader").hide();
      $("p.submit").show();
      $(".RemoveBG_Background_img").attr("src", "").css("display", "block");
      $(".pixcut_remove_bg-log-live").hide();
      $(".pixcut_remove_bg-process-stop").hide();

      $.ajax({
        type: "post",
        dataType: "json",
        url: ajaxurl,
        data: {
          action: "Pixcut_User_Aborted",
          Pixcut_RemoveBG_ID: $(".pixcut_remove_bg-process-stop").attr("data-id") || 0,
          _nonce: $("#_wpnonce").val(),
          schk: $("input#schk").val(),
        },
        success: function (data) {
          $("html, body").animate({ scrollTop: 0 }, "slow");
          if (data.hasErrors) {
            $("#status_restore_w").hide();
            $("#status_restore_e").show();
            $("#status_restore_e p").text(data.msg);
          } else {
            $("#status_restore_w").hide();
            $("#status_restore_d").show();
            $("#status_restore_d p").text(data.msg);
          }
          $("#loader").hide();
          $("p.submit").show();
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $("html, body").animate({ scrollTop: 0 }, "slow");
          $("#status_restore_e").show();
          $("#status_restore_e p").text(textStatus + " " + errorThrown);
          $("#loader").hide();
          $("p.submit").show();
        },
      });
    });

    $("#pixcut_restore_backup").on("click", function (e) {
      e.preventDefault();
      showModel($("#restore_backup_confirm").val(), function () {
        $("#loader").show();
        $("#previewresult").hide();
        $("p.submit").hide();
        $(".pixcut_remove_bg.notice").hide();
        $("#status_restore_w").show();
        $("#status_restore_e").hide();
        $("#status_restore_d").hide();
        $.ajax({
          type: "post",
          dataType: "json",
          url: ajaxurl,
          data: {
            action: "Pixcut_Remove_BG_Restore_Backup",
            _nonce: $("#_wpnonce").val(),
            schk: $("input#schk").val(),
          },
          success: function (data) {
            $("html, body").animate({ scrollTop: 0 }, "slow");
            if (data.hasErrors) {
              $("#status_restore_w").hide();
              $("#status_restore_e").show();
              $("#status_restore_e p").text(data.msg);
            } else {
              $("#status_restore_w").hide();
              $(".block-count").hide();
              $("#status_restore_d").show();
              $(".pixcut-button-click").addClass("d-none");
            }
            $("#loader").hide();
            $("p.submit").show();
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $("html, body").animate({ scrollTop: 0 }, "slow");
            $("#status_restore_e").show();
            $("#status_restore_e p").text(textStatus + " " + errorThrown);
            $("#loader").hide();
            $("p.submit").show();
          },
        });
      });
    });

    $("#pixcut_delete_backup").on("click", function (e) {
      e.preventDefault();
      showModel($("#delete_backup_confirm").val(), function () {
        $("#loader").show();
        $("p.submit").hide();
        $("#previewresult").hide();
        $(".pixcut_remove_bg.notice").hide();
        $.ajax({
          type: "post",
          dataType: "json",
          url: ajaxurl,
          data: {
            action: "Pixcut_Delete_backup",
            RemoveBG_Background: $('input[name="Pixcut_RemoveBG_Background"]:checked').val(),
            RemoveBG_Background_Color: $('input[name="Pixcut_RemoveBG_Background_Color"]').val(),
            _nonce: $("#_wpnonce").val(),
            schk: $("input#schk").val(),
          },
          success: function (data) {
            $("html, body").animate({ scrollTop: 0 }, "slow");
            if (data.hasErrors) {
              $("#status_restore_w").hide();
              $("#status_restore_e").show();
              $("#status_restore_e p").text(data.msg);
            } else {
              $(".pixcut-button-click").addClass("d-none");
              $(".block-count").hide();
              $("#status_restore_w").hide();
              $("#status_d").show();
              $("#status_d p").text(data.msg);
            }
            $("#loader").hide();
            $("p.submit").show();
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $("html, body").animate({ scrollTop: 0 }, "slow");
            $("#status_restore_e").show();
            $("#status_restore_e p").text(textStatus + " " + errorThrown);
            $("#loader").hide();
            $("p.submit").show();
          },
        });
      });
    });
    $("#buyCredits").on("click", function (e) {
      e.preventDefault();
      window.open("https://pixcut.wondershare.com/pricing.html", "_blank");
    });

    $("#start_preview").on("click", function (e) {
      e.preventDefault();
      $("#loader").show();
      $("p.submit").hide();
      $("#previewresult").hide();
      $(".pixcut_remove_bg.notice").hide();

      var form_data = new FormData();
      form_data.append("action", "Pixcut_Preview_BG_Images");
      form_data.append("Pixcut_RemoveBG_ApiKey", $('input[name="Pixcut_RemoveBG_ApiKey"]').val());
      form_data.append("Pixcut_RemoveBG_Background", $('input[name="Pixcut_RemoveBG_Background"]:checked').val());
      form_data.append("Pixcut_RemoveBG_Background_Color", $('input[name="Pixcut_RemoveBG_Background_Color"]').val());
      form_data.append("post_id", $('input[name="Pixcut_RemoveBG_TestProduct"]').val());
      form_data.append("_nonce", $("#_wpnonce").val());

      $.ajax({
        type: "post",
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
        url: ajaxurl,
        data: form_data,
        success: function (data) {
          if (data.hasErrors) {
            $("html, body").animate({ scrollTop: 0 }, "slow");
            $("#status_restore_w").hide();
            $("#status_restore_e").show();
            $("#status_restore_e p").text(data.msg);
          } else {
            $(".img-after-remove-bg").attr("src", data.file_after);
            $(".img-before-remove-bg").attr("src", data.file_before);
            $("#previewresult").css("display", "flex");
            $('input[name="Pixcut_RemoveBG_TestProduct"]').val("");
          }

          $("#loader").hide();
          $("p.submit").show();
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $("html, body").animate({ scrollTop: 0 }, "slow");
          $("#status_restore_e").show();
          $("#status_restore_e p").text(textStatus + " " + errorThrown);
          $("#loader").hide();
          $("p.submit").show();
        },
      });
    });

    var products = document.getElementsByName("Pixcut_RemoveBG_products");
    var products_inp = document.getElementsByName("Pixcut_RemoveBG_products_IDs")[0];
    for (var i = 0; i < products.length; i++) {
      products[i].addEventListener("change", function () {
        if (this.value == "specified") products_inp.removeAttribute("disabled");
        else products_inp.setAttribute("disabled", "disabled");
      });
    }
  });
  function showModel(desc, confirmCallback, succAction) {
    $("#model").show();
    $("#modelDesc").text(desc);
    if (confirmCallback) {
      console.log($(".model-action .btn")[0].css);
      $("#cancelAction").css("display", "block");
    }
    $(".model-action .btn")[0].addEventListener("click", function () {
      $("#model").hide();
    });
    $(".model-action .btn")[1].addEventListener("click", function () {
      if (succAction) {
        succAction();
        return;
      }
      if (confirmCallback) confirmCallback();
      $("#model").hide();
    });
  }
})(jQuery);
