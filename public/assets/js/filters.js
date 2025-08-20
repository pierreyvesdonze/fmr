document.addEventListener('DOMContentLoaded', function () {
    const resetButton = document.getElementById('resetFilters');
    const form = document.getElementById('filterForm');
    const spinner = document.querySelector('.spinner');

    if (!form || !resetButton) return;

    const filterButton = form.querySelector('button[type="submit"]');

    // Vérifie si un filtre est actif
    function checkFilters() {
        let isActive = false;
        const inputs = form.querySelectorAll('input, select');

        inputs.forEach(input => {
            if ((input.type === 'checkbox' || input.type === 'radio') && input.checked) {
                isActive = true;
            } else if (input.value && input.value.trim() !== '') {
                isActive = true;
            }
        });

        filterButton.disabled = !isActive;
    }

    checkFilters();

    form.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('change', checkFilters);
    });

    // Réinitialisation des filtres
    resetButton.addEventListener('click', function (event) {
        event.preventDefault();

        if (spinner) spinner.style.display = 'block';

        // Réinitialiser les inputs et selects
        form.querySelectorAll('input').forEach(input => {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });

        form.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0; // Remet au placeholder / première option
        });

        checkFilters();

        setTimeout(() => {
            if (spinner) spinner.style.display = 'none';
        }, 500);
    });
});
