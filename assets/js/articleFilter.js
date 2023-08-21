import { debounce } from 'lodash';
import { Flipper, spring } from 'flip-toolkit';

/**
 * Class Filter for search in AJAX
 * 
 * @property {HTMLElement} content - The list of the article on the page
 * @property {HTMLFormElement} form - The form for filter article
 * @property {HTMLElement} count - The number of items on the page
 * @property {HTMLElement} sorting - The button for sorting the result
 * @property {HTMLElement} pagination - The links for switch page for the search
 * @property {number} page - The number of the actual page
 * @property {bool} moreNav - If the navigation is with more button or navigation with pagination
 * 
 */
export class Filter {  // export default pour que la class soit exportable dans un autre fichier
    /**
     * 
     * @param {HTMLElement} element 
     * @returns 
     */
    constructor(element) {
        if(!element) {
            return;
        }

        // console.error(element);
        this.content = element.querySelector('.js-filter-content');
        this.form = element.querySelector('.js-filter-form');
        this.count = element.querySelector('.js-filter-count');
        this.sorting = element.querySelector('.js-filter-sorting');
        this.pagination = element.querySelector('.js-filter-pagination');
        this.page = parseInt(new URLSearchParams(window.location.search).get('page') || 1); // parseInt -> converti une chaine caract. en integer  - .search -> récup seulement les param get avec le "?"
        this.moreNav = this.page === 1;
        // console.error(this.page);
        // console.error(this.content, this.count, this.form, this.pagination, this.sorting);
        this.bindEvents(); // notre fonction/méthode        
    }

    /**
     * Add the action and the listener on HTMLElement
     */
    bindEvents() {
        const linkClickListener = async (e, scrollToTop) => {
            e.preventDefault();  // casse le comportement par defaut d'un élément HTML
            
            if(!e.target.classList.contains('disabled')) {
                let url;
    
                if(e.target.tagName === 'I' || e.target.tagName === 'SPAN') {  // tagname récupère le nom de la balise HTML (nom des balises en majuscule)
                    url = e.target.closest('[direction]').href; // on récupère le parent le plus proche qui possède l'attribut "direction" -> la balise a -> puis on prend son href 
                } else {
                    url = e.target.href;
                }
                // console.error(url);
                // console.error(e.target);
    
                await this.loadUrl(url);
    
                if(scrollToTop) {
                    window.scrollTo(0, 0);
                }
            }
        }

        this.sorting.addEventListener('click', linkClickListener);

        if(this.moreNav) {
            this.pagination.innerHTML = `<div class="text-center">
                                            <button class="btn btn-primary mt-2">Voir plus</button>
                                        </div>`;
            this.pagination.querySelector('button').addEventListener('click', this.loadMore.bind(this));
        } else {
            this.pagination.addEventListener('click', (e) => {
                linkClickListener(e, true);
            });
        }

        this.pagination.addEventListener('click', (e) => {
            linkClickListener(e, true);
        });

        this.form.querySelectorAll('input[type="text"]').forEach(input => { // on cherche tous les input de type text
            input.addEventListener('keyup', debounce(this.loadForm.bind(this), 400));  // avec bind(this) -> on force l'envoie de la class à loadForm
        });

        this.form.querySelectorAll('input[type="checkbox"]').forEach(input => {
            input.addEventListener('change', debounce(this.loadForm.bind(this), 1000)); //durée de l'inactivité en ms
        })

        this.form.querySelector('#btn-reset-form').addEventListener('click', this.resetForm.bind(this));
    }
    
    /**
     * Send AJAX request with url
     * @param {URL} url  - The url of AJAX request
     */
    async loadUrl(url, append = false) {
        this.showLoader();
        // console.error(url.split('?')); // on veut récupérer ce qu'il y a après le "?" dans l'url
        const urlParams = new URLSearchParams(url.split('?')[1] || '');  // si url.split('?')[1] renvoi undefined -> on lui donne une chaine de caractères vide
        urlParams.set('ajax', true); // on rajoute le paramètre get 'ajax' dans urlParams
        // console.error(urlParams.toString());  
        
        
        const response = await fetch(url.split('?')[0] + "?" + urlParams.toString()); // on envoie la requete AJAX et on attend la réponse du chemin d'accès qu'on lui a passé en paramètre
        // console.error(url.split('?')[0] + "?" + urlParams.toString());
        // console.error(await response.json());

        if(response.status >= 200 && response.status < 300) {
            const data = await response.json(); // on transforme la réponse en format Json
            
            this.sorting.innerHTML = data.sorting;
            
            this.animationContent(data.content, append);

            if(!this.moreNav) {
                this.pagination.innerHTML = data.pagination;
            } else if (this.page === data.totalPage) {
                this.pagination.style.display = 'none';
            } else {
                this.pagination.style.display = 'block';
            }
            
            this.count.innerHTML = data.count;

            urlParams.delete('ajax'); // il faut supprimer le paramètre get 'ajax' avant de renvoyer l'url
            history.replaceState({}, "", url.split('?')[0] + '?' + urlParams.toString());  // on change l'historique de l'url
            
            this.hideLoader();
        }
    }

    /**
     * Get all inputs of the form and send AJAX request with value
     */
    async loadForm() {
        this.page = 1;
        const data = new FormData(this.form);
        // console.error(data);
        const url = new URL(this.form.getAttribute('action') || window.location.href );
        // console.error(window.location.href); // récupère l'url actuelle du navigateur
        const urlParams = new URLSearchParams();

        data.forEach((value, key) => {
            // console.error(value, key);
            urlParams.append(key, value); // on rajoute les paramètres get dans l'url en fonction des paramètres de recherche cochés/rentrés
            // console.error(urlParams.toString());
        });

        return this.loadUrl(url.pathname + '?' + urlParams.toString());
    }

    /**
     * Reset search criteria
     */
    async resetForm() { // fonction pour réinitialiser la recherche
        const url = new URL(this.form.getAttribute('action') || window.location.href);

        this.form.querySelectorAll('input').forEach(input => {
            if(input.type === 'checkbox') { // décocher les cases
                input.checked = false;
            } else {
                input.value = '';
            }
        });

        return this.loadUrl(url.pathname.split('?')[0]);
    }
    
    /**
     * Load the next page with the more nav button
     */
    async loadMore() {
        const button = this.pagination.querySelector('button');
        button.setAttribute('disabled', true);
        this.page++;
        const url = new URL(window.location.href);
        const urlParams = new URLSearchParams(url.search);
        urlParams.set('page', this.page);  // on passe dans les paramètres get la nouvelle page
        
        await this.loadUrl(url.pathname + '?' + urlParams.toString(), true);

        button.removeAttribute('disabled');
    }

    /**
     * Add animation for update content
     * @param {string} newContent - String with HTML code of the new list of articles
     * @param {bool} append - If replace the content or append the existing content on the page
     */
    animationContent(newContent, append) {
        const springName = 'veryGentle'; // effet à la fin de l'animation =  ressort
        
        const exitAnimation = (element, index, onComplete) => {
            spring({ // pour créer une fonction d'animation custom avec flipper
                // config: 'stiff',
                values: {
                    translateY: [0, -20], // -20 : position du translateY à la fin de l'anim (parti de 0)
                    opacity: [1, 0]
                },
                onUpdate: ({ translateY, opacity }) => {  // fonction exécutée à la mise à jour
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                onComplete
            }); 
        }

        const appearAnimation = (element, index) => {
            spring({
                values: {
                    translateY: [-20, 0],
                    opacity: [0, 1]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 10,
            });
        }

        const flipper = new Flipper({
            element: this.content,
        });

        let articleCards = this.content.children;  // on récupère tous les éléments enfants de la <div class="blog-list">
        for(let card of articleCards) {
            flipper.addFlipped({
                element: card, // élément HTML à enregistrer
                flipId: card.id,
                spring: springName,
                onExit: exitAnimation,
            });
        }

        flipper.recordBeforeUpdate();

        if(append) {
            this.content.innerHTML += newContent;
        } else {
            this.content.innerHTML = newContent;
        }

        articleCards = this.content.children;  // enregistre le nouveau contenu de la page
        for(let card of articleCards) {
            flipper.addFlipped({
                element: card, 
                flipId: card.id,
                spring: springName,
                onAppear: appearAnimation,
            });
        }

        flipper.update();
    }

    /**
     * Show the loader of the form
     */
    showLoader() {  // fonction qui permet d'afficher le loader
        this.form.classList.add('is-loading');
        const loader = this.form.querySelector('.js-loading');
        loader.style.display = 'block';
        loader.setAttribute('aria-hidden', false);
    }
    
    /**
     * Hide the loader of the form
     */
    hideLoader() {  // fonction qui permet de cacher le loader
        this.form.classList.remove('is-loading');
        const loader = this.form.querySelector('.js-loading');
        loader.style.display = 'none';
        loader.setAttribute('aria-hidden', true);
    }

}