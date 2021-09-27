(function ($) {
  $(document).ready(function () {
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
          $("#model").hide();
          return;
        }
        if (confirmCallback) confirmCallback();
        $("#model").hide();
      });
    }
    function showToast(info, type) {
      const typeString = type === "success" ? "notice-success" : type === "info" ? "notice-info" : "error-info";
      $(".pixcut_remove_bg").hide();
      $(`.pixcut_remove_bg#${typeString}`).show();
      $(`.pixcut_remove_bg#${typeString} p`).html(info);
      $("html, body").animate({ scrollTop: 0 }, "slow");

      $("#loader").hide();
      $("p.submit").show();
    }

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
          Pixcut_RemoveBG_ID: remove_bg_id,
          schk: $("input#schk").val(),
          _nonce: $("#_wpnonce").val(),
        },
        success: function (data) {
          if (data.hasErrors == true) {
            if (data.error_msg === "Error: CREDIT_NOT_ENOUGH") {
              // TODO 后续任务直接弹出
              showModel("Credit not enough", null, function () {
                window.open("https://pixcut.wondershare.com/pricing.html");
              });
              $('#loader').hide();
              $('p.submit').show();
            } else {
              showToast(data.error_msg+" " + $(".pixcut_remove_bg-log").html() );
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
                  .html(data.success_msg + "  " + $(".pixcut_remove_bg-log").html() );
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
              showToast(data.success_msg + "  " + $(".pixcut_remove_bg-log").html(), "success");

              $(".pixcut_remove_bg-log-live").hide();
              $(".pixcut_remove_bg-process-stop").hide().attr("data-id", 0);
              $(".block-count").show();
            }
          }
          return true;
        },
      });
      return true;
    }

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

        $(`.pixcut_remove_bg#notice-info`).show();
        $(`.pixcut_remove_bg#notice-info p`).html("Background removal is in progress.");
        $(".pixcut_remove_bg#remove-ing").show();
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
                    showToast($("#alert-text-no-images").val());
                  }
                }
                if (data.hasErrors == true) {
                  ajaxQueue = $({});
                  processing = false;
                  showToast(data.error_msg);
                }
              }
              if (process == "save") {
                showToast("Settings have been saved.", "success");
                if ($('input[name="Pixcut_RemoveBG_ApiKey"]').val() != "") {
                  $("#appkeyWarning").hide();
                } else {
                  $("#appkeyWarning").show();
                }
              }
            },
            error: function (jqXHR, textStatus, errorThrown) {
              showToast(textStatus + " " + errorThrown);
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
            showToast(data.msg)
          } else {
           
            showToast(data.msg,'success')
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          showToast(textStatus + " " + errorThrown);
        },
      });
    });

    $("#pixcut_restore_backup").on("click", function (e) {
      e.preventDefault();
      showModel($("#restore_backup_confirm").val(), function () {
        showToast('Restore is in progress.','info')
        $("#loader").show();
        $("#previewresult").hide();
        $("p.submit").hide();
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
              showToast(data.msg);
            } else {
              showToast('Restore complete.', "success");
              $(".block-count").hide();
              $("#pixcut_restore_backup").addClass("d-none");
              $("#pixcut_delete_backup").addClass("d-none");
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            showToast(textStatus + " " + errorThrown);
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
            if (data.hasErrors) {
              showToast(data.msg);
            } else {
              showToast(data.msg, "success");
              $(".block-count").hide();
              $("#pixcut_restore_backup").addClass("d-none");
              $("#pixcut_delete_backup").addClass("d-none");
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            showToast(textStatus + " " + errorThrown);
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
            showToast(data.msg);
          } else {
            $(".img-after-remove-bg").attr("src", data.file_after);
            $(".img-before-remove-bg").attr("src", data.file_before);
            $("#previewresult").css("display", "flex");
            $('input[name="Pixcut_RemoveBG_TestProduct"]').val("");
            $("#loader").hide();
            $("p.submit").show();
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          showToast(textStatus + " " + errorThrown);
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
})(jQuery);
