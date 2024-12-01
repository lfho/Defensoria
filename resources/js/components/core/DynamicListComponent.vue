<template>
    <div :class="classContainer">
        <!-- Formulario de campos -->
        <form @submit.prevent="addToList()" class="form-horizontal">
            <!-- Campos de formulario -->
            <slot name="fields" :data-form="dataForm">
            </slot>
            <!-- Boton de guardado de datos de los campos ingresados-->
            <div class="form-group m-t-15">
                <button type="submit" :class="[classButtonAdd, 'btn btn-md']">
                    <i class="fa fa-plus mr-1"></i> {{ labelButtonAdd }}
                </button>
            </div>
        </form>
        <!-- Tabla de datos agregados desde el formulario -->
        <div class="row" v-if="dataList && dataList.length > 0">
            <div class="table-responsive">
                <table :class="['table', classTable]">
                    <thead>
                        <tr>
                            <!-- Obtiene los nombre de columna que se pueden visualizar -->
                            <th v-for="(option, key) in nameColumnOptions" :key="key">{{ option.label }}</th>
                            <th>{{ labelColActions }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Visualiza todos los elementos ingresados de la lista -->
                        <tr v-for="(row, key) in getDataColumValue" :key="key">
                            <td v-for="(col, k) in nameColumnOptions" :key="k">{{ col.nameObjectKey ? (col.nameObjectKey.length > 1 ? (row[col.nameObjectKey[0]] ? row[col.nameObjectKey[0]][col.nameObjectKey[1]] : row[col.name]) : (row[col.nameObjectKey[0]] instanceof Array ? row[col.nameObjectKey[0]]?.join(", ") : row[col.name])) : row[col.name] == true ? "Si" : (row[col.name] === false ? "No" : row[col.name]) }}</td>
                            <td>
                                <!-- Boton sube el elemento de la lista -->
                                <button v-if="key != 0" type="button" @click="moveUpElement(key)" :class="[classButtonDropElement, 'btn btn-icon btn-sm']">
                                    <i class="fa fa-arrow-up"></i>
                                </button>
                                <!-- Boton que baja el elemento de la lista -->
                                <button v-if="key != (dataList.length - 1)" type="button" @click="moveDownElement(key)" :class="[classButtonDropElement, 'btn btn-icon btn-sm']">
                                    <i class="fa fa-arrow-down"></i>
                                </button>
                                <!-- Boton que elimina un elemento de la lista -->
                                <button type="button" @click="dropElementList(key)" :class="[classButtonDropElement, 'btn btn-icon btn-sm']">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Prop, Component, Vue, Watch } from "vue-property-decorator";

import utility from '../../utility';

import axios from 'axios';

import {jwtDecode} from 'jwt-decode';

/**
 * Componente de formulario con lista dinamica
 *
 * @author Jhoan Sebastian Chilito S. - Ene. 06 - 2021
 * @version 1.0.0
 */
@Component
export default class DynamicListComponent extends Vue {

    /**
     * Clase css para el color del boton de agregar a la lista
     */
    @Prop({default: 'btn-primary'})
    public classButtonAdd: String;

    /**
     * Clase css para el color del boton de agregar a la lista
     */
    @Prop({default: 'btn-default'})
    public classButtonDropElement: String;

    /**
     * Clase css para contenedor del componente
     */
    @Prop({default: 'p-10'})
    public classContainer: String;

    /**
     * Clase css para el contenido de la tabla
     */
    @Prop({default: 'table-hover text-inverse'})
    public classTable: String;

    /**
     * Texto del boton de agregar a la lista
     */
    @Prop({default: 'Agregar a la lista'})
    public labelButtonAdd: String;

    /**
     * Texto del boton de agregar a la lista
     */
    @Prop({default: 'Acciones'})
    public labelColActions: String;

    /**
     * Lista de datos agregados
     */
    @Prop({required: true})
    public dataList: any;

    /**
     * Configuracion de nombres de lista dinamica
     *
     * Debe tener las siguientes propiedades:
     *  - label: Nombre de visualizacion de la propiedad (Nombre header tabla)
     *  - name: Nombre de la propiedad del objeto
     *  - isShow: Valida si se debe mostrar como columna de la tabla
     *  - refList: Nombre de select de referencia (Solamente para las listas (Select html))
     */
    @Prop({type: Array, default: () => []})
    public dataListOptions: any;

    /**
     * Campos del formulario
     */
    public dataForm: any;

    /**
     * Numero de peticiones pendientes
     */
    public numberOfAjaxCallPending: number;

    /**
     * Valida si no hay peticiones http
     */
    public isCharge: boolean;

    /**
     * Tiempo de espera para organizar
     */
    @Prop({type: Number, default: 100})
    public timeout: number;

    /**
     * Nombre de la variable que va a contener el objeto dataForm para almacenar los ids de los registros que se van a eliminar del listado de registros
     */
    @Prop({ type: String })
    public objEliminados: any;

    /**
     * Numero de niveles del dataForm
     */
     @Prop({type: Number, default: 1})
     public nivelesDataform: number;

     /**
      * El componente padre
      */
     public componentePadre: Vue;

    /**
     * Url para validar si es posible eliminar el registro seleccionado
     */
     @Prop({ type: String })
    public urlEliminarRegistro: any;

    /**
     * Constructor de la clase
     *
     * @author Jhoan Sebastian Chilito S. - Feb. 04 - 2021
     * @version 1.0.0
     */
    constructor() {
        super();
        this.dataForm = {};
        // Inicializa numero de peticiones pendientes
        this.numberOfAjaxCallPending = 0;
        // Inicializa si se ha cargado todas las peticiones
        this.isCharge = false;

        if(this.nivelesDataform == 4){
            this.componentePadre = this.$parent.$parent.$parent.$parent as Vue;
        } else if(this.nivelesDataform == 3){
            this.componentePadre = this.$parent.$parent.$parent as Vue;
        } else if(this.nivelesDataform == 2){
            this.componentePadre = this.$parent.$parent as Vue;
        } else {
            this.componentePadre = this.$parent as Vue;
        }

        this.$set(this.componentePadre["dataForm"], this.objEliminados, []);

        // Detecta las peticiones pendientes
        this.detectRequestPending();
    }

    /**
     * Detecta las peticiones pendientes
     *
     * @author Jhoan Sebastian Chilito S. - Feb. 04 - 2021
     */
    public detectRequestPending(): void {
         // Add a request interceptor
        axios.interceptors.request.use( (config) => {
            this.numberOfAjaxCallPending++;
            return config;
        }, (error) => {
            return Promise.reject(error);
        });

        // Add a response interceptor
        axios.interceptors.response.use( (response) => {
            this.numberOfAjaxCallPending--;
            return response;
        }, (error) => {
            this.numberOfAjaxCallPending--;
            return Promise.reject(error);
        });
    }

    /**
     * Detecta la cantidad de peticiones pendientes
     *
     * @author Jhoan Sebastian Chilito S. - Feb. 04 - 2021
     */
    @Watch('numberOfAjaxCallPending')
    public changePendingRequest() {
        // Valida si no hay peticiones en espera
        if (this.numberOfAjaxCallPending == 0) {
            // Cambia el estado para velidar que no hay peticiones pendientes
            setTimeout(() => {
                this.isCharge = true;
            }, this.timeout);
        } else {
            // Faltan peticiones por realizarse
            this.isCharge = false;
        }
    }

    /**
     * Agrega los datos del formulario ingresados a la lista
     *
     * @author Jhoan Sebastian Chilito S. - Ene. 08 - 2021
     * @version 1.0.0
     */
    public addToList(): void {
        // Agrega un nuevo elemento a la lista
        this.dataList.push(utility.clone(this.dataForm));
        // Limpia los campos del formulario
        this.dataForm = {};

        // console.log(this.$parent.$refs["ciudadanoRef"]);
        this.vaciarAutocompleteQuery();

    }

    /**
     * Elimina un elemento de la lista por la posicion
     *
     * @author Jhoan Sebastian Chilito S. - Ene. 08 - 2021
     * @version 1.0.0
     *
     * @param index indice del elemento a eliminar
     */
    public dropElementList(index: number): void {
        // Valida si el registro a eliminar tiene id, de ser así no es un registro recien ingresado, además si existe una url para consultar si el registro se puede elimnar o no
        if(this.dataList[index].id && this.urlEliminarRegistro) {
            // Petición a la urlEliminarRegistro para validar si se puede eliminar el registro
            axios.post(this.urlEliminarRegistro, this.dataList[index], { headers: { 'Content-Type': 'multipart/form-data' } })
            .then((res) => {
                let dataPayload = res.data.data ? jwtDecode(res.data.data) : null;
                // Si la propiedad data tiene algún valor, significa que no se puede eliminar el registro y se muestra un mensaje al usuario
                if(dataPayload["data"]) {
                    this.$swal({
                        icon: 'info',
                        html: dataPayload["data"],
                        confirmButtonText: this.componentePadre["lang"]["get"]('trans.Accept'),
                        allowOutsideClick: false,
                    });
                } else {
                    // Se registra el elemento eliminado en el arreglo de objetos elminados según la variable objEliminados
                    this.objEliminados && this.dataList[index].id ? this.componentePadre["dataForm"][this.objEliminados].push(this.dataList[index].id) : null;
                    // Si no retorna nada la variable data, quiere decir que se puede eliminar el registro sin nigún problema
                    Vue.delete(this.dataList, index);
                }
            })
            .catch((err) => {
                this.componentePadre["_pushNotification"]("Error al eliminar el registro"+err, false, 'Error');
            });
        } else {
            // Se registra el elemento eliminado en el arreglo de objetos elminados según la variable objEliminados
            this.objEliminados && this.dataList[index].id ? this.componentePadre["dataForm"][this.objEliminados].push(this.dataList[index].id) : null;
            // Se elimina el registro del listado
            Vue.delete(this.dataList, index);
        }
    }


    /**
     * Obtiene la referencia del selector
     *
     * @author Jhoan Sebastian Chilito S. - Ene. 21 - 2021
     * @version 1.0.0
     *
     * @param refName nombre de la referencia del selector
     */
    public getSelectRef(refName: string) {
        // Valida si es una referencia de un select normal
        if (this.$parent.$refs[refName]) {
            return this.$parent.$refs[refName];
        } else {
            // Recorre todos los componentes hijos
            for (let i = 0; i < this.$children.length; i++) {
                const childComponent = this.$children[i];
                // Valida que sea una referencia de un select de un componente hijo
                if (childComponent.$refs[refName]) {
                    return childComponent.$refs[refName];
                }
            }
        }
        return null;
    }

    /**
     * Elimina un elemento de la lista por la posicion
     *
     * @author Jhoan Sebastian Chilito S. - Ene. 08 - 2021
     * @version 1.0.0
     *
     * @param index indice del elemento a eliminar
     */
    public moveDownElement(index: number): void {
        this.move(this.dataList, index, 1);
    }

    /**
     * Elimina un elemento de la lista por la posicion
     *
     * @author Jhoan Sebastian Chilito S. - Ene. 08 - 2021
     * @version 1.0.0
     *
     * @param index indice del elemento a eliminar
     */
    public moveUpElement(index: number): void {
        this.move(this.dataList, index, -1);
    }

    /**
     * Mueve los elementos de un arreglo
     *
     * @param array lista a organizar
     * @param index ubicacion del elemento en la lista
     * @param delta variable de configuracion (1 = para bajar, -1 para subir) el elemento
     *
     * @see https://gist.github.com/albertein/4496103 documentacion de script
     */
    public move(array: any, index: number, delta: number): void {
        const newIndex = index + delta;
        if (newIndex < 0 || newIndex == array.length) return; // Already at the top or bottom.
        const indexes = [index, newIndex].sort((a, b) => a - b); // Sort the indixes (fixed)
        array.splice(indexes[0], 2, array[indexes[1]], array[indexes[0]]); // Replace from lowest index, two elements, reverting the order
    };

    /**
     * Obtiene el valor de la columna de la tabla por fila
     *
     * @author Jhoan Sebastian Chilito S. - Ene. 21 - 2021
     * @version 1.0.0
     */
    get getDataColumValue() {
        return utility.clone(this.dataList).map((data) => {
            this.nameColumnOptions.forEach(async (colOption) => {
                // Valida que sea una lista referida
                if (colOption.refList) {
                    await new Promise((resolve) => setTimeout(resolve, 2500))
                    .then(() => {
                        const selectRef = this.getSelectRef(colOption.refList);
                        // Valida si la referencia de la lista existe
                        if (selectRef) {
                            // Obtiene las opciones del select
                            const printerOpts = (selectRef as any).children;

                            if (colOption.selectItem) {

                                let datoColOption = data[colOption.name].map(Number);
                                // datoColOption.map(Number);

                                // console.log(datoColOption);
                                if (typeof datoColOption == 'object') {
                                    let options = [];
                                    // Recorre la lista de opiones
                                    for (let i = 0; i < printerOpts.length; i++) {
                                        const option = printerOpts[i];
                                        // Valida que la opcion de la lista sea la seleccionada
                                        if (datoColOption.includes(Number(option.value))) {
                                            // Obtiene el nombre del valor seleccionado
                                            options.push(option.text);
                                        }
                                    }
                                    data[colOption.name] = options.join(', ');
                                    this.$forceUpdate();
                                }
                            } else {
                                // Recorre la lista de opiones

                                if(printerOpts){
                                    for (let i = 0; i < printerOpts.length; i++) {
                                        const option = printerOpts[i];
                                        // Valida que la opcion de la lista sea la seleccionada
                                        if (option.value == data[colOption.name]) {
                                            // Obtiene el nombre del valor seleccionado
                                            data[colOption.name] = option.text;
                                            this.$forceUpdate();
                                        }
                                    }
                                }
                            }
                        } else {
                            // console.log('No existe la referencia de la la lista', colOption.label);
                        }
                    });
                }
                if (colOption.selectItem) {

                    if (typeof data[colOption.name] == 'object') {

                        const printerOpts = data[colOption.name];
                            // console.log(colOption);
                        let option = [];
                        // Recorre la lista de opiones
                        for (let i = 0; i < printerOpts.length; i++) {
                            option.push(printerOpts[i][colOption.selectItem]);
                        }
                        // console.log(option);
                        data[colOption.name] = option.join(', ');
                        this.$forceUpdate();
                    }
                }
            });
            return data;
        });
    }

    /**
     * Obtiene los nombre de columna que se puede visualizar
     *
     * @author Jhoan Sebastian Chilito S. - Ene. 08 - 2021
     * @version 1.0.0
     */
    get nameColumnOptions(): Array<any> {
        return this.dataListOptions.filter((option) => {
            return option.isShow;
        });
    }

    public vaciarAutocompleteQuery() {
        this.nameColumnOptions.forEach(async (colOption) => {
            // Valida que sea una lista referida
            if (colOption.refList) {
                (this.$parent.$refs[colOption.refList] && this.$parent.$refs[colOption.refList]["query"]) ? this.$parent.$refs[colOption.refList]["query"] = "" : null;
            }
        });
    }

}

</script>
