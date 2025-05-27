import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['menu']

    connect() {
        this.handleOutsideClick = this.handleOutsideClick.bind(this)
    }

    toggle(event) {
        event.preventDefault()
        this.isOpen() ? this.close() : this.open()
    }

    open() {
        this.menuTarget.classList.remove('hidden')
        this.menuTarget.classList.add('opacity-0', 'transition-opacity', 'duration-150', 'ease-out')
        requestAnimationFrame(() => {
            this.menuTarget.classList.remove('opacity-0')
            this.menuTarget.classList.add('opacity-100')
        })
        document.addEventListener('click', this.handleOutsideClick)
        document.addEventListener('keydown', this.handleEscape)
    }

    close() {
        this.menuTarget.classList.remove('opacity-100')
        this.menuTarget.classList.add('opacity-0')
        setTimeout(() => {
            this.menuTarget.classList.add('hidden')
        }, 150)
        document.removeEventListener('click', this.handleOutsideClick)
        document.removeEventListener('keydown', this.handleEscape)
    }

    isOpen() {
        return !this.menuTarget.classList.contains('hidden')
    }

    handleOutsideClick(e) {
        if (!this.element.contains(e.target)) {
            this.close()
        }
    }

    handleEscape = (e) => {
        if (e.key === 'Escape') {
            this.close()
        }
    }
}