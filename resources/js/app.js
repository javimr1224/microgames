import './bootstrap';
import './gsap_animations';

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
window.gsap = gsap;
gsap.registerPlugin(ScrollTrigger);

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

function initializeCardHoverEffects(container) {
    const cards = container.querySelectorAll('.game-card');
    cards.forEach(card => {
        const image = card.querySelector('.game-card-image');
        const video = card.querySelector('.game-card-video');

        if (image && video) {
            if (!card.dataset.hoverInitialized) {
                card.addEventListener('mouseenter', () => {
                    image.classList.add('hidden');
                    video.classList.remove('hidden');
                });

                card.addEventListener('mouseleave', () => {
                    video.classList.add('hidden');
                    image.classList.remove('hidden');
                });
                card.dataset.hoverInitialized = 'true';
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initializeCardHoverEffects(document);

    const searchIcon = document.getElementById('header-search-icon');
    const searchBar = document.getElementById('header-search-bar');
    const searchInput = document.getElementById('header-search-input');
    const searchResults = document.getElementById('header-search-results');

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    if (searchIcon && searchBar && searchInput && searchResults) {
        searchIcon.addEventListener('click', () => {
            const isHidden = searchBar.classList.contains('hidden');
            if (isHidden) {
                searchBar.classList.remove('hidden');
                gsap.fromTo(searchBar, { y: -20, opacity: 0 }, { y: 0, opacity: 1, duration: 0.3, ease: 'power1.out' });
                searchInput.focus();
            } else {
                gsap.to(searchBar, {
                    y: -20, opacity: 0, duration: 0.3, ease: 'power1.in', onComplete: () => {
                        searchBar.classList.add('hidden');
                    }
                });
            }
        });

        let latestQuery = '';
        const debouncedSearch = debounce(function (query) {
            latestQuery = query;
            axios.get('/search', {
                params: { query: query }
            })
                .then(response => {
                    if (query === latestQuery) {
                        searchResults.innerHTML = '';
                        if (response.data.results && response.data.results.length > 0) {
                            response.data.results.forEach(game => {
                                const resultItem = document.createElement('a');
                                const gameSlug = game.slug || game.name.toLowerCase();
                                resultItem.href = `/play/${encodeURIComponent(gameSlug)}?from=menu`;
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
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'p-2 text-red-500';
                    errorMsg.textContent = 'Error during search.';
                    searchResults.appendChild(errorMsg);
                });
        }, 300);

        searchInput.addEventListener('input', function (e) {
            const query = e.target.value;
            if (query.length > 2) {
                debouncedSearch(query);
            } else {
                searchResults.innerHTML = '';
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !searchBar.classList.contains('hidden')) {
                gsap.to(searchBar, {
                    y: -20, opacity: 0, duration: 0.3, ease: 'power1.in', onComplete: () => {
                        searchBar.classList.add('hidden');
                    }
                });
            }
        });

        document.addEventListener('click', (event) => {
            if (!searchBar.classList.contains('hidden') && !searchBar.contains(event.target) && !searchIcon.contains(event.target)) {
                gsap.to(searchBar, {
                    y: -20, opacity: 0, duration: 0.3, ease: 'power1.in', onComplete: () => {
                        searchBar.classList.add('hidden');
                    }
                });
            }
        });
    }

    fetch('/categories')
        .then(response => response.json())
        .then(categories => {
            const categoryDropdown = document.getElementById('category-dropdown-content');
            if (categoryDropdown) {
                categories.forEach(category => {
                    const link = document.createElement('a');
                    link.href = `/games/${category}`;
                    link.classList.add('block', 'px-4', 'py-2', 'text-sm', 'text-gray-300', 'hover:bg-gray-700', 'hover:text-white');
                    link.textContent = category;
                    categoryDropdown.appendChild(link);
                });
            }
        })
        .catch(error => console.error('Error fetching categories:', error));

    const filterPopularBtn = document.getElementById('filter-popular-welcome');
    const filterNewestBtn = document.getElementById('filter-newest-welcome');
    const gamesContainer = document.getElementById('welcome-games-container');
    const placeholderGifUrl = "/videos/may-sitting-near-waterfall-pokemon-emerald-pixel-wallpaperwaifu-com-ezgif.com-video-to-gif-converter.gif";

    function createGameCard(game) {
        const imageUrl = game.image_url || game.image;
        const videoUrl = game.video_url || game.video || placeholderGifUrl;

        return `
            <div class="bg-neutral-primary-soft border-[3px] border-default rounded-xl shadow-xs game-card transition-transform duration-200 hover:scale-105">
                    <img class="rounded-t-xl border-b-[3px] border-default game-card-image w-full h-48 object-cover"
                        src="${imageUrl}" alt="${game.name}" />
                    <img src="${videoUrl}"
                        class="rounded-t-xl hidden w-full h-48 object-cover game-card-video" />
                </a>
                <div class="p-4 sm:p-6 text-start">
                    <a href="/play/${encodeURIComponent(game.slug)}?from=menu">
                        <h5 style="font-family: 'Press Start 2P';" class="mt-1 mb-4 sm:mb-6 text-sm sm:text-base">
                            ${game.name}</h5>
                        <p style="font-family: 'Helvetica Neue';" class="mt-2 text-sm">${game.description}</p>
                    </a>
                </div>
            </div>
        `;
    }

    async function fetchGames(filterType) {
        try {
            const response = await fetch(`/api/games/filter/${filterType}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const games = await response.json();
            if (gamesContainer) {
                gamesContainer.innerHTML = '';
                games.slice(0, 3).forEach(game => {
                    gamesContainer.innerHTML += createGameCard(game);
                });
                initializeCardHoverEffects(gamesContainer);
            }
        } catch (error) {
            console.error("Could not fetch games:", error);
            if (gamesContainer) {
                gamesContainer.innerHTML = '<p class="text-center col-span-full">No se pudieron cargar los juegos.</p>';
            }
        }
    }

    if (filterPopularBtn && filterNewestBtn && gamesContainer) {
        filterPopularBtn.addEventListener('click', () => fetchGames('popular'));
        filterNewestBtn.addEventListener('click', () => fetchGames('newest'));

        fetchGames('popular');
    }
    
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

    if (typeof gsap === 'undefined') {
        console.error('GSAP no está cargado');
        return;
    }

    gsap.utils.toArray(['.sprite-top', '.sprite-left', '.sprite-right-1', '.sprite-right-2']).forEach(sprite => {
        if (sprite) {
            gsap.from(sprite, {
                scale: 0,
                rotation: 360,
                duration: 0.8,
                ease: 'back.out(1.7)',
                scrollTrigger: {
                    trigger: sprite,
                    start: 'top 85%',
                    toggleActions: 'play none none none',
                }
            });

            gsap.to(sprite, {
                y: -10,
                duration: 1.5,
                repeat: -1,
                yoyo: true,
                ease: 'power1.inOut',
                scrollTrigger: {
                    trigger: sprite,
                    start: 'top 85%',
                }
            });
        }
    });

    const card1 = document.querySelector('.game-card-1');
    if (card1) {
        gsap.from(card1, {
            x: -200,
            opacity: 0,
            scale: 0.8,
            rotation: -5,
            duration: 1,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: card1,
                start: 'top 75%',
                toggleActions: 'play none none none',
            }
        });

        card1.addEventListener('mouseenter', () => {
            gsap.to(card1, {
                scale: 1.05,
                rotation: 2,
                duration: 0.3,
                ease: 'power2.out'
            });
        });

        card1.addEventListener('mouseleave', () => {
            gsap.to(card1, {
                scale: 1,
                rotation: 0,
                duration: 0.3,
                ease: 'power2.out'
            });
        });
    }

    const card2 = document.querySelector('.game-card-2');
    if (card2) {
        gsap.from(card2, {
            y: -300,
            opacity: 0,
            rotation: 180,
            duration: 1.2,
            ease: 'bounce.out',
            scrollTrigger: {
                trigger: card2,
                start: 'top 75%',
                toggleActions: 'play none none none',
            }
        });

        card2.addEventListener('mouseenter', () => {
            gsap.to(card2, {
                y: -15,
                scale: 1.03,
                duration: 0.4,
                ease: 'power2.out'
            });
        });

        card2.addEventListener('mouseleave', () => {
            gsap.to(card2, {
                y: 0,
                scale: 1,
                duration: 0.4,
                ease: 'power2.out'
            });
        });
    }

    const card3 = document.querySelector('.game-card-3');
    if (card3) {
        gsap.from(card3, {
            x: -250,
            opacity: 0,
            scale: 0.5,
            duration: 1,
            ease: 'elastic.out(1, 0.5)',
            scrollTrigger: {
                trigger: card3,
                start: 'top 75%',
                toggleActions: 'play none none none',
            }
        });

        card3.addEventListener('mouseenter', () => {
            gsap.to(card3, {
                scale: 1.08,
                rotation: -3,
                duration: 0.3,
                ease: 'power2.out'
            });

            const img = card3.querySelector('img');
            if (img) {
                gsap.to(img, {
                    scale: 1.1,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            }
        });

        card3.addEventListener('mouseleave', () => {
            gsap.to(card3, {
                scale: 1,
                rotation: 0,
                duration: 0.3,
                ease: 'power2.out'
            });

            const img = card3.querySelector('img');
            if (img) {
                gsap.to(img, {
                    scale: 1,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            }
        });
    }

    const card4 = document.querySelector('.game-card-4');
    if (card4) {
        gsap.from(card4, {
            x: 250,
            opacity: 0,
            scale: 0.5,
            duration: 1.2,
            ease: 'elastic.out(1, 0.6)',
            scrollTrigger: {
                trigger: card4,
                start: 'top 75%',
                toggleActions: 'play none none none',
            }
        });

        card4.addEventListener('mouseenter', () => {
            gsap.to(card4, {
                scale: 1.1,
                rotation: 4,
                duration: 0.35,
                ease: 'power3.out'
            });

            const img = card4.querySelector('img');
            if (img) {
                gsap.to(img, {
                    scale: 1.12,
                    duration: 0.35,
                    ease: 'power3.out'
                });
            }
        });

        card4.addEventListener('mouseleave', () => {
            gsap.to(card4, {
                scale: 1,
                rotation: 0,
                duration: 0.35,
                ease: 'power3.out'
            });

            const img = card4.querySelector('img');
            if (img) {
                gsap.to(img, {
                    scale: 1,
                    duration: 0.35,
                    ease: 'power3.out'
                });
            }
        });
    }

    const description = document.querySelector('.game-card-1')?.closest('section')?.querySelector('p');
    if (description) {
        gsap.from(description, {
            opacity: 0,
            y: 30,
            duration: 0.8,
            delay: 0.3,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: description,
                start: 'top 80%',
                toggleActions: 'play none none none',
            }
        });
    }

    const exitSprites = document.querySelectorAll(
        "img[src*='dancingcowboydone21.gif'], img[src*='pjpa72551.gif'], img[src*='walk_011'], img[src*='hideyoshi']"
    );

    exitSprites.forEach(sprite => {
        gsap.from(sprite, {
            opacity: 0,
            scale: 0.7,
            duration: 2.3,
            ease: "power2.out",
            scrollTrigger: {
                trigger: sprite,
                start: "top 90%",
                toggleActions: "play none none none",
            }
        });
    });

    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const moonIcon = document.getElementById('moon-icon');
    const sunIcon = document.getElementById('sun-icon');
    const darkThemeLink = document.getElementById('dark-theme-link');
    const lightThemeLink = document.getElementById('light-theme-link');

    const enableDarkMode = () => {
        document.documentElement.classList.remove('light-mode');
        localStorage.setItem('theme', 'dark');
        if (moonIcon && sunIcon) {
            sunIcon.style.display = 'none';
            moonIcon.style.display = 'block';
        }
    };

    const enableLightMode = () => {
        document.documentElement.classList.add('light-mode');
        localStorage.setItem('theme', 'light');
        if (moonIcon && sunIcon) {
            moonIcon.style.display = 'none';
            sunIcon.style.display = 'block';
        }
    };

    if (localStorage.getItem('theme') === 'light') {
        enableLightMode();
    } else {
        enableDarkMode();
    }

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            if (document.documentElement.classList.contains('light-mode')) {
                enableDarkMode();
            }
            else {
                enableLightMode();
            }
        });
    }

    if (darkThemeLink) {
        darkThemeLink.addEventListener('click', (e) => {
            e.preventDefault();
            enableDarkMode();
        });
    }

    if (lightThemeLink) {
        lightThemeLink.addEventListener('click', (e) => {
            e.preventDefault();
            enableLightMode();
        });
    }
});
