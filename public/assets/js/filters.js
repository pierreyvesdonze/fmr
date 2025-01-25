document.addEventListener('DOMContentLoaded', function () {
    const resetButton = document.getElementById('resetFilters');
    const form = document.getElementById('filterForm');
    const filterButton = form.querySelector('button[type="submit"]');
    const spinner = document.querySelector('.spinner');
    
    // Fonction pour vérifier si un filtre est sélectionné
    function checkFilters() {
        let isActive = false;
        const inputs = form.querySelectorAll('input, select');

        inputs.forEach(input => {
            if (input.type === 'checkbox' || input.type === 'radio') {
                if (input.checked) isActive = true;
            } else if (input.value) {
                isActive = true;
            }
        });

        // Active ou désactive le bouton selon si un filtre est sélectionné
        if (isActive) {
            filterButton.disabled = false;
        } else {
            filterButton.disabled = true;
        }
    }

    // Désactive le bouton "Filtrer" dès le chargement de la page si aucun filtre n'est sélectionné
    checkFilters();

    // Ajoute un écouteur d'événements sur les champs du formulaire
    form.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('change', checkFilters);  // Met à jour quand un filtre est modifié
    });

    // Réinitialise les filtres et désactive le bouton
    if (resetButton && form) {
        resetButton.addEventListener('click', function (event) {
            event.preventDefault();

            // Afficher le spinner lors de la réinitialisation
            if (spinner) {
                spinner.style.display = 'block'; // Affiche le spinner
            }

            // Réinitialiser les champs du formulaire
            let inputs = form.querySelectorAll('input, select');
            inputs.forEach(function (input) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = false;
                } else {
                    input.value = '';
                }
            });

            checkFilters();  // Vérifie si le bouton doit être activé ou non
        });
    }
});
