import './bootstrap';
import './gsap_animations';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const dropdownContainers = document.querySelectorAll('.dropdown-container');

    dropdownContainers.forEach(container => {
        const dropdown = container.querySelector('.dropdown-content');
        let leaveTimeout;

        if (dropdown) {
            container.addEventListener('mouseenter', function () {
                clearTimeout(leaveTimeout);
                dropdownContainers.forEach(otherContainer => {
                    if (otherContainer !== container) {
                        const otherDropdown = otherContainer.querySelector('.dropdown-content');
                        if (otherDropdown) {
                            otherDropdown.classList.remove('show');
                        }
                    }
                });
                dropdown.classList.add('show');
            });

            container.addEventListener('mouseleave', function () {
                leaveTimeout = setTimeout(() => {
                    dropdown.classList.remove('show');
                }, 300);
            });
        }
    });
});
