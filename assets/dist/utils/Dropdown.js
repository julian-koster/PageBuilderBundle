export class Dropdown {
    constructor(element, menu) {
        this.element = element
        this.menu = menu
        this.handleOutsideClick = this.handleOutsideClick.bind(this)
        this.handleEscape = this.handleEscape.bind(this)
    }

    toggle(event) {
        event.preventDefault()
        this.isOpen() ? this.close() : this.open()
    }

    open() {
        this.menu.classList.remove('hidden')
        this.menu.classList.add('opacity-0', 'transition-opacity', 'duration-150', 'ease-out')
        requestAnimationFrame(() => {
            this.menu.classList.remove('opacity-0')
            this.menu.classList.add('opacity-100')
        })
        document.addEventListener('click', this.handleOutsideClick)
        document.addEventListener('keydown', this.handleEscape)
    }

    close() {
        this.menu.classList.remove('opacity-100')
        this.menu.classList.add('opacity-0')
        setTimeout(() => {
            this.menu.classList.add('hidden')
        }, 150)
        document.removeEventListener('click', this.handleOutsideClick)
        document.removeEventListener('keydown', this.handleEscape)
    }

    isOpen() {
        return !this.menu.classList.contains('hidden')
    }

    handleOutsideClick(e) {
        if (!this.element.contains(e.target)) {
            this.close()
        }
    }

    handleEscape(e) {
        if (e.key === 'Escape') {
            this.close()
        }
    }
}
