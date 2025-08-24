import { Controller } from '@hotwired/stimulus';
import { Dropdown, Collapse } from 'bootstrap'; // Import only the components you need

/*
 * This is a generic Stimulus controller to initialize Bootstrap components.
 * It ensures that components are re-initialized after a Turbo page load.
 */
export default class extends Controller {
    connect() {
        // Initialize all dropdowns on the page
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        dropdownElementList.map(function (dropdownToggleEl) {
            return new Dropdown(dropdownToggleEl);
        });

        // Initialize all collapsible elements (like your accordion)
        const collapseElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="collapse"]'));
        collapseElementList.map(function (collapseEl) {
            // We only initialize if it hasn't been initialized already
            return Collapse.getOrCreateInstance(collapseEl);
        });
    }
}


// Note: Fix bootstrap js on collapsed items.
// Need to add data-controller="bootstrap" attribute to body for it to work.
