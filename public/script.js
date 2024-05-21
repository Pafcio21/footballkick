    document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="player[]"]');
            const submitButton = document.getElementById('submit');

            function validateCheckboxes() {
                const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
                if (checkedCount >= 4 && checkedCount % 2 === 0) {
                    submitButton.disabled = false;
                } else {
                    submitButton.disabled = true;
                }
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', validateCheckboxes);
            });
        });
