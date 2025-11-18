import { gsap } from 'gsap';

document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.animatable-button');

    buttons.forEach(button => {
        button.addEventListener('mousedown', () => {
            gsap.to(button, {
                y: 5, 
                scale: 0.95, 
                duration: 0.1,
                ease: "power1.out"
            });
        });

        button.addEventListener('mouseup', () => {
            gsap.to(button, {
                y: 0,
                scale: 1,
                duration: 0.2,
                ease: "elastic.out(1, 0.3)"
            });
        });

        button.addEventListener('mouseleave', () => {
            gsap.to(button, {
                y: 0,
                scale: 1,
                duration: 0.2,
                ease: "elastic.out(1, 0.3)"
            });
        });
    });
});
