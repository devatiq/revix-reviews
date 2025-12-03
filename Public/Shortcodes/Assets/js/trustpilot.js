document.addEventListener('DOMContentLoaded', function() {
    var loader = document.querySelector('.revix-loader-wrapper');
    var reviews = document.querySelector('.revix-trustpilot-reviews');
    
    if (loader) {
        loader.style.display = 'none';
    }
    
    if (reviews) {
        reviews.style.display = 'grid';
    }
});