import './bootstrap';
import { initScrollAnimationsAndMobileMenu } from './scroll-animations';

document.addEventListener('DOMContentLoaded', initScrollAnimationsAndMobileMenu);
document.addEventListener('livewire:navigated', initScrollAnimationsAndMobileMenu);
