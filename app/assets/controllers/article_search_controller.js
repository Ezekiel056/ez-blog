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
            fetch(`/articles/search?q=${encodeURIComponent(q)}`)
                .then(r => r.text())
                .then(html => {
                    this.listTarget.innerHTML = html;
                });

        }, 300); // délai en ms
    }


}
