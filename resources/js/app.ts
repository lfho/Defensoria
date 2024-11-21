/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// importa funcionalidades de toast de manera general
window.toastr = require('toastr');

import './bootstrap';

import Vue from "vue";
import VuePrintNB from 'vue-print-nb';

import { Cropper } from 'vue-advanced-cropper';
import vSelect from 'vue-select';
import { VueRecaptcha } from 'vue-recaptcha';
import { jwtDecode } from 'jwt-decode';

import VueSweetalert2 from 'vue-sweetalert2';
import VCalendar from 'v-calendar';
import Verte from 'verte';
// Libreria para el formato de moneda en los inputs
import VueCurrencyInput from 'vue-currency-input';
//libreria de tablas
import { TableComponent, TableColumn } from 'vue-table-component';
import  StarRating  from "vue-star-rating";
import wysiwyg from "vue-wysiwyg";

import VueMultiselect from 'vue-multiselect';
import BootstrapVue from 'bootstrap-vue';
// import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

// Estilos componente select2
import 'vue-select/dist/vue-select.css';
// Estilos componente sweetalert2
import 'sweetalert2/dist/sweetalert2.min.css';

import "vue-wysiwyg/dist/vueWysiwyg.css";

import 'verte/dist/verte.css';
import 'vue-multiselect/dist/vue-multiselect.min.css';


/**
 * Importacion de componentes locales
 */
import ExampleComponent from './components/ExampleComponent.vue';
import AutoCompleteFieldComponent from './components/core/AutoCompleteFieldComponent.vue';
import CrudComponent from './components/core/CrudComponent.vue';
import CropperImageComponent from './components/CropperImageComponent.vue';
import DatePickerComponent from './components/core/DatePickerComponent.vue';
import DynamicListComponent from './components/core/DynamicListComponent.vue';
import DynamicModalFormComponent from './components/core/DynamicModalFormComponent.vue';
import SelectCheckCrudFieldComponent from './components/core/SelectCheckCrudFieldComponent.vue';
import SelectCheckCrudFieldComponentDepend from './components/core/SelectCheckCrudFieldComponentDepend.vue';
import InputCrudFileComponent from './components/core/InputCrudFileComponent.vue';
import WidgetComponent from './components/WidgetComponent.vue';
import WidgetComponentV2 from './components/WidgetComponentV2.vue';
import EditProfilePage from './pages/EditProfilePage.vue';
import AddListAutoCompleteComponent from './components/core/AddListAutoCompleteComponent.vue';
import ChartComponent from './components/core/ChartComponent.vue';
import HolidayCalendarsComponent from './pages/HolidayCalendarsComponent.vue';
import ColourPickerComponent from './pages/ColourPickerComponent.vue';
import AddListOptionComponent from './components/core/AddListOptionComponent.vue';
import ExecutionFromActionComponent from './components/core/ExecutionFromActionComponent.vue';
import TextAreaEditorComponent from './components/core/TextAreaEditorComponent.vue';
import SendPetitionMessageComponent from './components/core/SendPetitionMessageComponent.vue';
import MultiselectComponent from './components/core/MultiselectComponent.vue';
import AlertConfirmationComponent from './components/core/AlertConfirmationComponent.vue';
import CapacityComponent from './components/core/CapacityComponent.vue';

import ViewerAtthachementComponent from './components/core/ViewerAtthachementComponent.vue';
import ChatGptComponent from './components/core/ChatGptComponent.vue';

import SignToImageComponent from './components/core/SignToImageComponent.vue';
import SignExternalComponent from './components/core/SignExternalComponent.vue';
import AnnotationsGeneralComponent from './components/core/AnnotationsGeneralComponent.vue';
import ExpedientesGeneralComponent from './components/core/ExpedientesGeneralComponent.vue';

import ViewerPublic from './components/core/ViewerPublic.vue';

// Componentes del módulo de mesa de ayuda
import AssetsTics from './pages/help_table/AssetsTics.vue';
import AddTechnicalTicComponent from './pages/help_table/AddTechnicalTicComponent.vue';
import RequestTicHistories from  './pages/help_table/RequestTicHistories.vue';
import TicHTAssetMaintenance from  './pages/help_table/TicHTAssetMaintenance.vue';
import HtTicSatisfactionPoll from  './pages/help_table/HtTicSatisfactionPoll.vue';
import HtTicKnowledgeBase from  './pages/help_table/HtTicKnowledgeBase.vue';

//Componentes para el modulo de hojas de vida
import Approve from  './pages/Workhistories/Approve.vue';

//Intranet encuestas
import PollAnswers from  './pages/intranet/poll/PollAnswers.vue';
//Intranet calendario
import CalendarEventComponent from './pages/intranet/CalendarEventComponent.vue';

import SearchUniversal from './components/core/SearchUniversalComponent.vue';

import SearchResultUniversal from './components/core/SearchResultUniversalComponent.vue';

import InternalComponent from  './pages/correspondence/InternalComponent.vue';
import MetadatosComponent from  './pages/DocumentaryClassification/MetadatosComponent.vue';
import FormCriteriosBusqueda from './pages/DocumentaryClassification/FormCriteriosBusqueda.vue';
import CriteriosBusquedaSave from './pages/DocumentaryClassification/CriteriosBusquedaSave.vue'

import ExternalComponent from  './pages/correspondence/ExternalComponent.vue';
import AnnotationAndRead from  './pages/correspondence/AnnotationAndRead.vue';
import CorrespondenceValidatorComponent from  './pages/correspondence/CorrespondenceValidatorComponent.vue';
import DatePikerMultipleComponent from  './pages/correspondence/DatePikerMultipleComponent.vue';

import ShareCorrespondenceUser from  './pages/correspondence/ShareCorrespondenceUser.vue';

import VueDragResize from 'vue-drag-resize';

import ExternalReceived from './pages/correspondence/ExternalReceived.vue';

//PQRS
import PQRComponent from  './pages/pqr/PQRComponent.vue';
import axios from 'axios';

// Dashboard
import DashboardComponent from  './pages/dashboard/DashboardComponent.vue';

// Documentos electrónicos
import DocumentosElectronicosComponent from  './pages/DocumentosElectronicos/DocumentoComponent.vue';
import DocumentosValidatorComponent from  './pages/DocumentosElectronicos/DocumentosValidatorComponent.vue';
import RotuleComponent from  './pages/correspondence/RotuleComponent.vue';

const Lang = require('lang.js');

// Configuracion de archivo de traduccion
let lang = new Lang({
    messages: {
        'es.trans': require('../lang/es.json')
    },
    fallback: 'es'
});

Vue.use(VueSweetalert2); // Componente sweetalert2
Vue.use(VCalendar);// Componente calendario de fechas
Vue.use(VueCurrencyInput); // Componente formato moneda
// Vue.use(VueApexCharts);
Vue.use(wysiwyg, {});
Vue.use(VuePrintNB);
Vue.use(BootstrapVue);

Vue.component('crud', CrudComponent); // Componente de crud general
// Vue.component('v-paginate', Paginate) // Componente paginador para tablas
Vue.component('v-select', vSelect); // Componente selec2
Vue.component('vue-recaptcha', VueRecaptcha); // Componente reCaptcha de Google
Vue.component('example-component', ExampleComponent); // Componente de ejemplo
Vue.component('autocomplete', AutoCompleteFieldComponent); // Componente de campo autocompletado
Vue.component('cropper', Cropper); // Componente de recorte y subida de imagenes
Vue.component('cropper-image', CropperImageComponent); // Componente propio de recorte y subida de imagenes
Vue.component('date-picker', DatePickerComponent); // Componente de date picker general
Vue.component('dynamic-list', DynamicListComponent); // Componente de lista dinamica
Vue.component('dynamic-modal-form', DynamicModalFormComponent); // Componente de modal dinamico
Vue.component('select-check', SelectCheckCrudFieldComponent); // Componente de select o check general para crud
Vue.component('select-check-depend', SelectCheckCrudFieldComponentDepend); // Componente de select o check general para crud, dependiente de otro select
Vue.component('input-file', InputCrudFileComponent); // Componente para subir archivos al servidor
Vue.component('widget-counter', WidgetComponent); // Componente de select o check general para crud
Vue.component('widget-counter-v2', WidgetComponentV2); // Componente en V2 para mostrar las cards en el tablero de los trámites
Vue.component('add-list-autocomplete', AddListAutoCompleteComponent); // Componente para agregar a una lista desde un autocomplete
Vue.component('add-list-option', AddListOptionComponent); // Componente para agregar a una lista desde un autocomplete
Vue.component('execution-from-action', ExecutionFromActionComponent); // Componente para ejecutar una ruta desde una accion
Vue.component('text-area-editor', TextAreaEditorComponent); // Componente mostrar un textarea con texto enriquecido
Vue.component('multiselect', VueMultiselect);
Vue.component('multiselect-component', MultiselectComponent);
Vue.component('calendar-event', CalendarEventComponent); // Componente para agregar a una lista desde un autocomplete
Vue.component('send-petition-message', SendPetitionMessageComponent);//Envia una peticion al servidor y hace una confirmacion con sweetalert2 y actualiza el elemento que se afecte
Vue.component('table-component', TableComponent);
Vue.component('table-column', TableColumn);
Vue.component('star-rating', StarRating); // Componente de estrellas para crud general
Vue.component('alert-confirmation',AlertConfirmationComponent) // Componente para mostrar una alerta de confirmacion
Vue.component('storage-capacity',CapacityComponent) // Componente para mostrar el espacio y la cantidad de usuarios

Vue.component('viewer-attachement',ViewerAtthachementComponent) // Componente para mostrar una alerta de confirmacion

Vue.component('chat-gpt-component',ChatGptComponent) // Componente para integrar inteligencia artificial

Vue.component('sign-to-image',SignToImageComponent) // Componente para mostrar una alerta de confirmacion
Vue.component('sign-external-component',SignExternalComponent) // Componente para mostrar una alerta de confirmacion
Vue.component('annotations-general',AnnotationsGeneralComponent) // Componente de anotaciones
Vue.component('viewer-public',ViewerPublic) // Componente para mostrar una alerta de confirmacion
Vue.component('expedientes-general',ExpedientesGeneralComponent) // Componente de expedienetes

// Paginas
Vue.component('edit-profile-page', EditProfilePage); // Componente de select o check general para crud
// Vue.component('apexchart', VueApexCharts);
Vue.component('chart-component', ChartComponent); // Componente de select o check general para crud

Vue.component('verte', Verte); // Componente de select o check general para crud

Vue.component('holiday-calendar', HolidayCalendarsComponent); // Componente de select o check general para crud
Vue.component('colour-picker', ColourPickerComponent); // Componente de select o check general para crud

//Intranet encuestas
Vue.component('intranet-poll-answer', PollAnswers); // Componente de respuesta de encuestas


Vue.component('search-universal', SearchUniversal);
Vue.component('search-result-universal', SearchResultUniversal);
Vue.component('vue-drag-resize', VueDragResize);


Vue.component('correspondence-internal', InternalComponent);
Vue.component('correspondence-external', ExternalComponent);
Vue.component('annotation-and-read', AnnotationAndRead);
Vue.component('correspondence-validator', CorrespondenceValidatorComponent);
Vue.component('date-piker-multiple', DatePikerMultipleComponent);

Vue.component('external-received', ExternalReceived);

Vue.component('share-correspondence-user', ShareCorrespondenceUser);

Vue.component('pqr-component', PQRComponent); // Componente de PQRS
Vue.component('metadatos-component', MetadatosComponent);
Vue.component('form-criterios-busqueda', FormCriteriosBusqueda);
Vue.component('criterios-busqueda-save', CriteriosBusquedaSave);


// Componentes de mesa de ayuda
Vue.component('assets-tics', AssetsTics); // Componente de select o check general para crud
Vue.component('add-technical-tic', AddTechnicalTicComponent); // Componente de select o check general para crud
Vue.component('tic-ht-request-histories', RequestTicHistories); // Componente de select o check general para crud
Vue.component('tic-ht-asset-maintenance', TicHTAssetMaintenance); // Componente de select o check general para crud
Vue.component('ht-tic-satisfaction-poll', HtTicSatisfactionPoll); // Componente de select o check general para crud
Vue.component('ht-tic-knowledge-base', HtTicKnowledgeBase); // Componente de select o check general para crud

// Dashboard
Vue.component('dashboard', DashboardComponent);

// Documentos electrónicos
Vue.component('documentos-electronicos', DocumentosElectronicosComponent);
Vue.component('documentos-validator', DocumentosValidatorComponent);
Vue.component('rotule-component', RotuleComponent);

// Filtro para convertir texto a mayuscula
Vue.filter('uppercase', (value: string) => {
	return value.toUpperCase();
});
// Filtro para convertir texto a minuscula
Vue.filter('lowercase', (value: string) => {
	return value.toLowerCase();
});
// Filtro para convertir la primera letra en mayuscula y el resto en minuscula
Vue.filter('capitalize', (value: string) => {
    return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
});
// Filtro para la traduccion
Vue.filter('trans', (...value: string[]) => {
    return lang.get(...value);
});




Vue.prototype.$log = console.log;

new Vue({
    el: '#app',
    data() {
        return {
            lang: lang,
            query: '',
            dataForm: {},
            dataList: [],
            searchFields: {},
            // Controla si la sesión del usuario esta activa o no
            sessionExpired: false
        };
    },
    created() {
        // Obtiene la URL actual de la página
        let url = window.location.href;

        setInterval( function(){
            /**
             * Valida la url de la página, si es la indicada en la condición, no valida la sesión;
             * si la variable sessionExpired es true, la sesión a expirado o es inactiva.
             */


            if(url.indexOf("firmar") == -1 && url.indexOf("validate-correspondence-received-email") == -1 && url.indexOf("validate-correspondence-external-email") == -1  && url.indexOf("validar-codigo") == -1 && url.indexOf("register") == -1 && url.indexOf("p-q-r-s-ciudadano-anonimo") == -1 && url.indexOf("password") == -1 && !this.sessionExpired && url.indexOf("validar-correspondence-external") == -1 && url.indexOf("validar-correspondence-internal") == -1 && url.indexOf("search-pqrs-ciudadano") == -1 && url.indexOf("validar-documento-electronico") == -1  && url.indexOf("survey-satisfaction-pqrs") == -1) {
                this.checkAuth();
            }
        }.bind(this), 20000);
    },
    methods: {
        // Valida el estado de la sesión del usuario
        checkAuth() {
            // Realiza la petición para validar la sesión
            axios.get('/check-session')
            .then((res) => {
                let dataPayload = res.data.data ? jwtDecode(res.data.data) : null;

                const dataDecrypted = Object.assign({}, dataPayload);

                // Si data = true, la sesión esta activa, de lo contrario, esta inactiva
                if(!dataDecrypted["data"]) {
                    this.sessionExpired = true;
                    // Muestra un mensaje al usuario indicando que la sesión ha expirado
                    this.$swal({
                        icon: "info",
                        html: "Tu sesión ha expirado. Por favor, inicia sesión de nuevo",
                        allowOutsideClick: false,
                        confirmButtonText: this.lang.get('trans.Accept')
                    }).then(() => {
                        // Redirecciona al usuario a la página de login, para iniciar sesión nuevamente
                        window.location.href = "/login";
                    });
                }
            })
            .catch((err) => {
                console.log("Error validando la sesión del usuario: "+err);
            });
        }
    }
});
