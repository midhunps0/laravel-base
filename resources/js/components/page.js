import axios from 'axios';

export default () => ({
    page: null,
    ajax: false,
    showPage: true,
    ajaxLoading: false,
    async initAction() {
        let link = window.landingUrl;
        console.log('lu: '+link);
        let el = document.getElementById('renderedpanel');
        while (el == null) {
            await window.sleep(50);
            el = document.getElementById('renderedpanel');
        }
        this.$store.app.xpages[link] = el.innerHTML;
        history.pushState({href: link}, '', link);
        // axios.get(link, {data: {"x_mode": 'ajax'}} ).then(
        //     (r) => {
        //         this.$store.app.xpages = [];
        //         this.$store.app.xpages[link] = r.data;
        //         history.pushState({href: link}, '', link);
        //     }
        // ).catch(
        //     function (e) {
        //         console.log(e);
        //     }
        // );
    },
    historyAction(e) {
        if (e.state != undefined && e.state != null) {
            let link = e.state.href;
            this.showPage = false;
            this.ajaxLoading = true;
            if (this.$store.app.xpages[link] != undefined) {
                setTimeout(() => {
                    this.showPage = true;
                    this.page = this.$store.app.xpages[link];
                    this.$dispatch('pagechanged', {currentpath: link});
                    this.ajaxLoading = false;
                },
                    100
                );
            } else {
                setTimeout(() => {
                    this.showPage = true;},
                    100
                );
            }
        }
    },
    fetchLink(link) {
        if (this.$store.app.xpages != undefined && this.$store.app.xpages[link] != undefined) {
            this.showPage = false;
            this.ajaxLoading = true;
            if (this.$store.app.xpages[link] != undefined) {
                setTimeout(() => {
                    this.showPage = true;
                    this.page = this.$store.app.xpages[link];
                    this.$dispatch('pagechanged', {currentpath: link});
                    this.ajaxLoading = false;
                },
                    100
                );
            } else {
                setTimeout(() => {
                    this.showPage = true;
                    this.ajaxLoading = false;
                },
                    100
                );
            }
            history.pushState({href: link}, '', link);
        } else {
            this.$store.app.pageloading = true;
            // this.$dispatch('pageload');
            axios.get(link, {params: {x_mode: 'ajax'}} ).then(
                (r) => {
                    this.showPage = false;
                    this.ajaxLoading = true;
                    this.ajax = true;
                    setTimeout(
                        () => {
                            if(document.getElementById('renderedpanel') != null) {document.getElementById('renderedpanel').remove();}
                            this.page = r.data;
                            this.showPage = true;
                            this.ajaxLoading = false;
                        },
                        100
                    );
                    if (this.$store.app.xpages == undefined || this.$store.app.xpages == null) {
                        this.$store.app.xpages = [];
                    }
                    this.$store.app.xpages[link] = r.data;
                    history.pushState({href: link}, '', link);
                    this.$store.app.pageloading = false;
                    this.$dispatch('pagechanged', {currentpath: link});
                }
            ).catch(
                function (e) {
                    console.log(e);
                }
            );
            // this.$store.app.pageloading = false;
        }
    },
    resetPages() {
        this.$store.app.xpages = [];
        console.log('pages reset');
    }
});