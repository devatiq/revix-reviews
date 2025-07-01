jQuery(document).ready(function ($) {
  $("#revixreviews-feedback-form").on("submit", function (e) {
    e.preventDefault();

    const formData = $(this).serializeArray();
    formData.push({ name: "nonce", value: revixreviews_ajax_obj.nonce });

    $.post(revixreviews_ajax_obj.ajax_url, formData, function (response) {
      if (response.success) {
        const redirectUrl = revixreviews_ajax_obj.redirect_url || "/";
        Swal.fire({
          title: "Thank you!",
          text: response.data.message,
          icon: "success",
          timer: 10000, //  auto-close after 10 seconds
          timerProgressBar: true,
          showConfirmButton: true, // still show OK button
        }).then(() => {
          window.location.href = redirectUrl;
        });
        $("#revixreviews-feedback-form")[0].reset();
      } else {
        Swal.fire({
          title: "Error!",
          text: response.data.message,
          icon: "error",
          timer: 10000, //  auto-close after 10 seconds
          timerProgressBar: true,
          showConfirmButton: true, // still show OK button
        });
      }
    }).fail(function () {
      Swal.fire({
        title: "Server Error!",
        text: "Something went wrong. Please try again.",
        icon: "error",
        timer: 10000, //  auto-close after 10 seconds
        timerProgressBar: true,
        showConfirmButton: true, // still show OK button
      });
    });
  });
});
