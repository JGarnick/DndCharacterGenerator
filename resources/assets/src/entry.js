import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

const requireComponent = require.context(
    // The relative path of the components folder
    '../js/components',
    // Whether or not to look in subfolders
    true,
    // The regular expression used to match base component filenames
    /.+.(vue)/
);

requireComponent.keys().forEach(fileName => {
    const componentConfig = requireComponent(fileName);
    fileName = fileName.split("/");
    fileName = fileName[fileName.length - 1];
    const componentName = "v-" + fileName.replace(/.vue$/, '').toLowerCase();
    Vue.component(
        componentName,
        componentConfig.default || componentConfig
    );
});
$(document).ready(() => {
    const store = new Vuex.Store({

        state: {
            char: {
                level: 1,
                name: "Test",
                race: "Human",
                subrace: false,
                cclass: "Barbarian",
                p_bonus: 2,
                ac: 13,
                hp_max: 13,
                hp_current: 13,
                str: 15,
                dex: 14,
                con: 13,
                int: 12,
                wis: 10,
                cha: 8,
                proficiencies: [],
                saves: ["str", "con"],
                darkvision: false,
                speed: 30,
                passive_perception: 10,
                num_atks: 1
            },
            data: {
                races: window.races,
                subraces: window.subraces,
                cattributes: window.cattributes
            }
        },
        actions: {
            changeRace: function({commit}, payload){
                commit('mutateChangeRace', payload);
            },
            changeSubrace: function({commit}, payload){
                commit('mutateChangeSubrace', payload);
            },
            changeStr: function({commit}, payload){
                commit('mutateChangeStr', payload);
            },
            changeDex: function({commit}, payload){
                commit('mutateChangeDex', payload);
            },
            changeCon: function({commit}, payload){
                commit('mutateChangeCon', payload);
            },
            changeInt: function({commit}, payload){
                commit('mutateChangeInt', payload);
            },
            changeWis: function({commit}, payload){
                commit('mutateChangeWis', payload);
            },
            changeCha: function({commit}, payload){
                commit('mutateChangeCha', payload);
            },
        },
        mutations: {
            mutateChangeRace: function(state, payload){
                state.char.race = payload;
            },
            mutateChangeSubrace: function(state, payload){
                state.char.subrace = payload;
            },
            mutateChangeStr: function(state, payload){
                state.char.str = payload;
            },
            mutateChangeDex: function(state, payload){
                state.char.dex = payload;
            },
            mutateChangeCon: function(state, payload){
                state.char.con = payload;
            },
            mutateChangeInt: function(state, payload){
                state.char.int = payload;
            },
            mutateChangeWis: function(state, payload){
                state.char.wis = payload;
            },
            mutateChangeCha: function(state, payload){
                state.char.cha = payload;
            },
        },
        getters: {
            characterRace(state){
                return state.char.race;
            },
            characterSubrace(state){
                return state.char.subrace;
            },
            characterAttributes(state){
                return state.char.atts;
            }
        }
    });

    var app = new Vue({
        el: "#vue-1",
        store,
        data(){
            return {
                left_tabs: [
                    {title: "Race", active: true, content: "v-race-content"},
                    {title: "Ability Scores", active: false, content: "v-ability-scores-content"},
                    {title: "Background", active: false, content: "v-background-content"},
                    {title: "Class / Level", active: false, content: "v-class-level-content"},
                    {title: "Spells", active: false, content: "v-spells-left-content"},
                    {title: "Proficiencies", active: false, content: "v-proficiencies-left-content"},
                    {title: "Equipment", active: false, content: "v-equipment-left-content"},
                ],
                right_tabs: [
                    {title: "Summary", active:true, content: "v-summary-content"},
                    {title: "Combat", active:false, content: "v-combat-content"},
                    {title: "Proficiencies", active:false, content: "v-proficiencies-right-content"},
                    {title: "Spells", active:false, content: "v-spells-right-content"},
                    {title: "Features", active:false, content: "v-features-content"},
                    {title: "Equipment", active:false, content: "v-equipment-right-content"},
                ]
            }
        },
    });
    
});
