<template>
    <nav class="sticky top-0 z-40 w-full backdrop-blur flex-none transition-colors duration-500 lg:z-50 lg:border-b lg:border-slate-900/10 dark:border-slate-50/[0.06] bg-white supports-backdrop-blur:bg-white/95 dark:bg-slate-900/75">
        <div class="inline-flex items-center xl:text-lg">
            Pimcore Icon Sheet
        </div>
        <div class="inline-flex space-x-2 items-center">
            <button type="button" title="Toggle to dark mode" class="dark:hidden" @click="toggleTheme">
                <SunIcon class="h-6 w-6 " />
            </button>
            <button type="button" title="Toggle to light mode" class="hidden dark:block" @click="toggleTheme('light')">
                <MoonIcon class="h-6 w-6" />
            </button>
            <a href="https://github.com/Muetze42/pimcore-icon-sheet">
                <svg
                    viewBox="0 0 16 16"
                    class="w-5 h-5"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z" />
                </svg>
            </a>
        </div>
    </nav>
    <main class="mt-6 max-w-7xl mx-auto p-4 mb-24">
        <div class="text-center">
            Set:
            <select class="form-select py-1 rounded text-black" v-model="selectedSet" @change="changeSet">
                <option v-for="set in sets" :value="set">{{ set }}</option>
            </select>
        </div>
        <div class="flex flex-wrap justify-center space-x-4 space-y-4 mt-6">
            <div v-for="icon in icons" class="p-2 w-80 rounded border text-center dark:bg-slate-800 dark:border-slate-600">
                <img :src="'assets/pimcoreadmin/'+icon" :alt="icon" class="icon mx-auto">
                <Copy :value="'/bundles/pimcoreadmin/img/'+icon" name="Path" />
                <div v-if="icon in css" class="mt-2 rounded border border-dashed border-amber-500/50 pb-1">
                    Class:
                    <Copy :value="css[icon]" name="Class" />
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import {
    SunIcon,
    MoonIcon,
} from '@heroicons/vue/24/solid'

import CSS from './../../../docs/data/css.json';
import SETS from './../../../docs/data/sets.json';
import Copy from "./Copy";

export default {
    components: {
        SunIcon, MoonIcon,
        Copy
    },
    data() {
        return {
            sets: SETS,
            css: CSS,
            selectedSet: localStorage.set ? localStorage.set : 'material-icons',
            icons: {},
        }
    },
    mounted() {
        this.setColorMode()
        this.changeSet()
    },
    methods: {
        getSVG(path) {
            let svg = ''
            axios.get('/assets/pimcoreadmin/'+path)
                .then(response => {
                    svg = response.data
                })
            return svg;
        },
        changeSet() {
            localStorage.set = this.selectedSet
            axios.get('/data/sets/'+this.selectedSet+'.json')
                .then(response => {
                    this.icons = response.data
                })
        },
        toggleTheme(theme = 'dark') {
            theme === 'light' ?  localStorage.theme = 'light' : localStorage.removeItem('theme')
            this.setColorMode()
        },
        setColorMode() {
            localStorage.theme === 'light' ?
                document.documentElement.classList.remove('dark') :
                document.documentElement.classList.add('dark')
        }
    }
}
</script>

<style scoped>

</style>
