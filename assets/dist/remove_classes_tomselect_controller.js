import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    connect() {
        this.cleanupTomSelect();
    }

    cleanupTomSelect() {
        setTimeout(() => {
            const wrapper = document.querySelector(".ts-wrapper");

            if (wrapper) {
                 const classesToRemove = [
                    "text-gray-900",
                    "bg-gray-50",
                    "rounded-lg",
                    "text-sm",
                    "block",
                    "w-full",
                    "p-2.5",
                    "border",
                    "border-gray-300",
                    "focus:z-10",
                    "focus:ring-blue-500",
                    "focus:border-blue-500",
                    "dark:bg-gray-700",
                    "dark:border-gray-600",
                    "dark:text-white",
                    "dark:placeholder-gray-400",
                    "dark:focus:ring-blue-500",
                    "dark:focus:border-blue-500"
                 ];

                // Find all select elements and ts-wrapper divs within the parent div
                const elements = document.querySelectorAll(".ts-wrapper");

                elements.forEach(element => {
                    classesToRemove.forEach(className => {
                        element.classList.remove(className);
                    });
                });
            }
        }, 100); // Small delay of 100ms
    }
}
