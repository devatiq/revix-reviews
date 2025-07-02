document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('#revixreviews-feedback-form .star');
    const ratingInput = document.getElementById('revixreviews_rating');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = parseInt(star.getAttribute('data-value'), 10);
            ratingInput.value = value;

            stars.forEach(s => {
                s.classList.remove('selected');
                if (parseInt(s.getAttribute('data-value'), 10) <= value) {
                    s.classList.add('selected');
                }
            });
        });
    });
});
