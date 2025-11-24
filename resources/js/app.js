import './bootstrap';
import './gsap_animations';

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
window.gsap = gsap;
gsap.registerPlugin(ScrollTrigger);

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const searchIcon = document.getElementById('header-search-icon');
    const searchBar = document.getElementById('header-search-bar');
    const searchInput = document.getElementById('header-search-input');
    const searchResults = document.getElementById('header-search-results');

    if (searchIcon && searchBar && searchInput && searchResults) {
        // Toggle search bar visibility with GSAP
        searchIcon.addEventListener('click', () => {
            const isHidden = searchBar.classList.contains('hidden');
            if (isHidden) {
                searchBar.classList.remove('hidden');
                gsap.fromTo(searchBar, { y: -20, opacity: 0 }, { y: 0, opacity: 1, duration: 0.3, ease: 'power1.out' });
                searchInput.focus();
            } else {
                gsap.to(searchBar, { y: -20, opacity: 0, duration: 0.3, ease: 'power1.in', onComplete: () => {
                    searchBar.classList.add('hidden');
                }});
            }
        });

        // Perform search on input
        searchInput.addEventListener('input', function (e) {
            const query = e.target.value;
            searchResults.innerHTML = '';

            if (query.length > 2) {
                axios.get('/search', { // Corrected URL
                    params: { query: query }
                })
                .then(response => {
                    if (response.data.results && response.data.results.length > 0) {
                        response.data.results.forEach(game => {
                            const resultItem = document.createElement('a');
                            resultItem.href = `/games/${game.id}`;
                            resultItem.className = 'block p-2 text-left text-white hover:bg-gray-700 rounded-md';
                            resultItem.textContent = game.name;
                            searchResults.appendChild(resultItem);
                        });
                    } else {
                        const noResults = document.createElement('p');
                        noResults.className = 'p-2 text-gray-400';
                        noResults.textContent = 'No results found.';
                        searchResults.appendChild(noResults);
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'p-2 text-red-500';
                    errorMsg.textContent = 'Error during search.';
                    searchResults.appendChild(errorMsg);
                });
            }
        });
    }

    // GSAP ScrollTrigger for game cards
    gsap.utils.toArray('.game-card').forEach(card => {
        gsap.from(card, {
            opacity: 0,
            y: 50,
            duration: 0.6,
            ease: 'power1.out',
            scrollTrigger: {
                trigger: card,
                start: 'top 80%', // Animation starts when the top of the card is 80% from the top of the viewport
                toggleActions: 'play none none none', // Only play the animation once
            }
        });

        const image = card.querySelector('.game-card-image');
        const video = card.querySelector('.game-card-video');

        if (image && video) {
            card.addEventListener('mouseenter', () => {
                image.classList.add('hidden');
                video.classList.remove('hidden');
                video.play();
            });

            card.addEventListener('mouseleave', () => {
                video.classList.add('hidden');
                image.classList.remove('hidden');
                video.pause();
                video.currentTime = 0;
            });
        }
    });
});

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
