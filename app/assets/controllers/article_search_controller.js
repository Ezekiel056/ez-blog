import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['input', 'list']



    connect() {
        this.timeout = null;
    }

    search() {
        clearTimeout(this.timeout);

        //SetTimeout evite de spam le serveur a chaque touche et introduit une latence de 300ms entre les recherche

        this.timeout = setTimeout(() => {

            const q = this.inputTarget.value;

            fetch('/articles/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    q: q
                })
            })
                .then(r => r.text())
                .then(html => {
                    this.listTarget.innerHTML = html;

                    // mise a jour de l'url dans le navigateur
                    const url = new URL(window.location);
                    if (q) {
                        url.searchParams.set('q', encodeURI(q));
                    } else {
                        url.searchParams.delete('q');
                    }
                    window.history.replaceState({}, '', url);


                });

        }, 300); // délai en ms
    }


}
