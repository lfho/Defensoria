<template>
    <div>
        <!-- begin profile -->
        <div class="profile">
            <div class="profile-header">
                <!-- BEGIN profile-header-cover -->
                <div class="profile-header-cover"></div>
                <!-- END profile-header-cover -->
                <!-- BEGIN profile-header-content -->
                <div class="profile-header-content">
                    <!-- BEGIN profile-header-img -->
                    <div class="profile-header-img">
                        <img v-if="dataUser.profile_img_preview"  :src="dataUser.profile_img_preview" alt="">
                    </div>
                    <!-- END profile-header-img -->
                    <!-- BEGIN profile-header-info -->
                    <div class="profile-header-info">
                        <h4 class="mt-0 mb-1">{{ dataUser.name }}</h4>
                        <p class="mb-0">{{ (dataUser.positions)? dataUser.positions.nombre : '' }}</p>
                        <p class="mb-2">{{ (dataUser.dependencies)? dataUser.dependencies.nombre : '' }}</p>
                    </div>
                    <!-- END profile-header-info -->
                </div>
                <!-- END profile-header-content -->
                <!-- BEGIN profile-header-tab -->
                <ul class="profile-header-tab nav nav-tabs">
                    <li class="nav-item"><a href="#profile-post" class="nav-link active" data-toggle="tab"></a></li>
                </ul>
                <!-- END profile-header-tab -->
            </div>
            <form @submit.prevent="save()" enctype="multipart/form-data">
                <div class="panel" data-sortable-id="ui-general-1">
                    <!-- begin panel-heading -->
                    <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Estructura organizacional</strong></h4>
                    </div>
                    <!-- end panel-heading -->
                    <!-- begin panel-body -->
                    <div class="panel-body">
                        <div class="row">
                            <!-- Id Cargo Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4">{{ 'trans.positions' | trans }}:</label>
                                    <div class="col-md-8">
                                        <label class="form-control">{{ (dataUser.positions)? dataUser.positions.nombre : '' }}</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Id Dependencia Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4">{{ 'trans.dependencies' | trans }}:</label>
                                    <div class="col-md-8">
                                        <label class="form-control">{{ (dataUser.dependencies)? dataUser.dependencies.nombre : ''}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end panel-body -->
                </div>

                <div class="panel" data-sortable-id="ui-general-1">
                    <!-- begin panel-heading -->
                    <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Detalles de la cuenta de usuario</strong></h4>
                    </div>
                    <!-- end panel-heading -->
                    <div class="alert hljs-wrapper fade show">
                        <!--<span class="close" data-dismiss="alert">×</span>-->
                        <i class="fa fa-info fa-2x pull-left m-r-10"></i>
                        <p class="m-b-0">Solo se podrán modificar los siguientes campos: Nombre, Nombre de usuario, Contraseña, Imágen de perfil y ¿Deseo recibir notificaciones al correo?</p>
                    </div>
                    <!-- begin panel-body -->
                    <div class="panel-body">
                        <div class="row">
                            <!-- Name Field -->
                            <div class="col-md-12">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-2 required">{{ 'trans.Name' | trans }}:</label>
                                    <div class="col-md-10">
                                        <input type="text" id="name" :class="{'form-control':true, 'is-invalid':dataErrors.name }" v-model="dataUser.name" required >
                                        <small>Ingrese el nombre completo del funcionario (Nombres y apellidos).</small>
                                        <div class="invalid-feedback" v-if="dataErrors.name">
                                            <p class="m-b-0" v-for="(error, key) in dataErrors.name" :key="key">@{{ error }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Username Field -->
                            <!-- <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4 required">{{ 'trans.Username' | trans }}:</label>
                                    <div class="col-md-8">
                                        <input type="text" id="username" :class="{'form-control':true, 'is-invalid':dataErrors.username }" v-model="dataUser.username" required >
                                        <small>Ingrese el nombre de usuario que va a utilizar en la cuenta.</small>
                                        <div class="invalid-feedback" v-if="dataErrors.username">
                                            <p class="m-b-0" v-for="(error, key) in dataErrors.username" :key="key">@{{ error }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <!-- Password Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4 required">{{ 'trans.Password' | trans }}:</label>
                                    <div class="col-md-8">
                                        <input type="password" id="password" :class="{'form-control':true, 'is-invalid':dataErrors.password }" v-model="dataUser.password" >
                                        <small>Ingrese una contraseña que tenga mínimo 8 caracteres, debe contener al menos una letra mayúscula, una minúscula, un número y un símbolo - /; 0) & @.?! %*.</small>
                                        <div class="invalid-feedback" v-if="dataErrors.password">
                                            <p class="m-b-0" v-for="(error, key) in dataErrors.password" :key="key">@{{ error }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Confirm Password Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4 required">{{ 'trans.Confirm Password' | trans }}:</label>
                                    <div class="col-md-8">
                                        <input type="password" id="password_confirmation" :class="{'form-control':true, 'is-invalid':dataErrors.password }" v-model="dataUser.password_confirmation" >
                                        <small>Por favor confirme la contraseña que ingresó.</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="col-md-12"/>
                            <!-- Second Password Field -->
                            <div class="col-md-12">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-2">Habilitar segunda contraseña:</label>
                                    <div class="col-md-8">
                                        <input type="checkbox" name="enable_second_password" id="enable_second_password" v-model="dataUser.enable_second_password"><br />
                                        <small>Habilite esta opción si requiere que sea solicitada una segunda contraseña a la hora de publicar un documento.</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Second Password Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4">Segunda contraseña:</label>
                                    <div class="col-md-8">
                                        <input type="password" id="second_password" :class="{'form-control':true, 'is-invalid':dataErrors.second_password }" v-model="dataUser.second_password" >
                                        <small>Ingrese una contraseña que contenga mínimo 6 caracteres. Esta contraseña se usará para la publicación de documentos.</small>
                                        <div class="invalid-feedback" v-if="dataErrors.second_password">
                                            <p class="m-b-0" v-for="(error, key) in dataErrors.second_password" :key="key">{{ error }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Confirm Second Password Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4">Confirmar segunda contraseña:</label>
                                    <div class="col-md-8">
                                        <input type="password" id="second_password_confirmation" :class="{'form-control':true, 'is-invalid':dataErrors.second_password_confirmation }" v-model="dataUser.second_password_confirmation" >
                                        <small>Por favor confirme la segunda contraseña que ingresó.</small>
                                    </div>
                                </div>
                            </div>
                            <hr class="col-md-12"/>

                            <!-- Email Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4">{{ 'trans.Email' | trans }}:</label>
                                    <div class="col-md-8">
                                        <label class="form-control">{{ dataUser.email }}</label>
                                        <small>Ingrese un correo electrónico válido, ej: xxxxx@gmail.com</small>
                                        <div class="invalid-feedback" v-if="dataErrors.email">
                                            <p class="m-b-0" v-for="(error, key) in dataErrors.email" :key="key">@{{ error }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Block Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4">{{ 'trans.Block' | trans }}:</label>
                                    <!-- switcher -->
                                    <div class="col-md-8">
                                        <label class="form-control">{{ dataUser.block? 'trans.yes' : 'trans.no' | trans}}</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Url Img Profile Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4">{{ 'trans.Url Img Profile' | trans }}:</label>
                                    <div class="col-md-8" v-if="false">
                                        <cropper
                                            classname="cropper"
                                            :src="dataUser.profile_img_preview"
                                            :stencil-props="{
                                                previewClassname: 'preview'
                                            }"
                                            stencil-component="circle-stencil"
                                            ref="cropper">
                                        </cropper>
                                        <input type="file" name="url_img_profile" id="url_img_profile" @change="inputFile($event, 'url_img_profile')" accept="image/*">
                                        <small>Relacione una imagen al perfil de la cuenta.</small>
                                    </div>
                                    <div class="col-md-8">
                                        <cropper-image :img="urlProfileImagePreview" :value="dataUser" name-field="url_img_profile"></cropper-image>
                                    </div>
                                </div>
                            </div>
                            <!-- Url Digital Signature Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label id="url_digital_signature" class="col-form-label col-md-4">{{ 'trans.Digital Signature' | trans }}: </label>
                                    <div class="col-md-8">
                                        <img width="150" class="img-responsive" v-if="dataUser.url_digital_signature" :src="urlStorage + dataUser.url_digital_signature" alt="">
                                    </div>
                                    <div class="col-md-8">

                                        <input type="file" name="url_digital_signature" id="url_digital_signature" @change="inputFile($event, 'url_digital_signature')" accept="image/*">

                                    </div>
                                </div>
                            </div>
                            <!-- Sendemail Field -->
                            <div class="col-md-6">
                                <div class="form-group row m-b-15">
                                    <label class="col-form-label col-md-4">{{ 'trans.Sendemail' | trans }}:</label>
                                    <!-- Sendemail switcher -->
                                    <div class="switcher col-md-6 m-t-5">
                                        <input type="checkbox" name="sendEmail" id="sendEmail" v-model="dataUser.sendEmail">
                                        <label for="sendEmail"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end panel-body -->
                </div>

                <div class="panel" data-sortable-id="ui-general-1">
                    <!-- begin panel-heading -->
                    <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title"><strong>Asignaciones en el sistema</strong></h4>
                    </div>
                    <!-- end panel-heading -->
                    <!-- begin panel-body -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel">
                                    <div class="panel-heading ui-sortable-handle">
                                        <h4 class="panel-title"><strong>Roles y permisos</strong></h4><br>
                                    </div>
                                    <div class="alert hljs-wrapper fade show">
                                        <!--<span class="close" data-dismiss="alert">×</span>-->
                                        <i class="fa fa-info fa-2x pull-left m-r-10 m-t-5"></i>
                                        <p class="m-b-0">Son permisos otorgados a los usuarios a partir de roles que le permiten llevar a acabo ciertas tareas en los sistemas.</p>
                                    </div>
                                    <div class="panel-body">
                                        <!-- Checks Roles -->
                                        <ul>
                                            <li v-for="(role, key) in dataUser.roles" :key="key">
                                                {{ role.name }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel">
                                    <div class="panel-heading ui-sortable-handle">
                                        <h4 class="panel-title"><strong>Grupos de trabajo</strong></h4>
                                    </div>
                                    <div class="alert hljs-wrapper fade show">
                                        <!--<span class="close" data-dismiss="alert">×</span>-->
                                        <i class="fa fa-info fa-2x pull-left m-r-10 m-t-5"></i>
                                        <p class="m-b-0">Son grupos de funcionarios que dan uso a las herramientas colaborativas del intranet según sus intereses organizacionales.</p>
                                    </div>
                                    <div class="panel-body">
                                        <!-- Checks Roles -->
                                        <ul>
                                            <li v-for="(wg, key) in dataUser.work_groups" :key="key">
                                                {{ wg.name }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end panel-body -->
                </div>

                <button type="submit" class="btn btn-primary m-b-15"><i class="fa fa-save mr-2"></i>Guardar</button>
            </form>
        </div>
        <!-- end profile -->
    </div>
</template>

<script lang="ts">
import { Component, Prop, Watch, Vue } from "vue-property-decorator";

import axios from "axios";
import { jwtDecode } from 'jwt-decode';
import * as bootstrap from 'bootstrap';

import IUser from "../models/user/IUser";

/**
 * Componente para la edicion del perfil del usuario logueado
 *
 * @author Jhoan Sebastian Chilito S. - Ago. 04 - 2020
 * @version 1.0.0
 */
@Component
export default class EditProfilePage extends Vue {

    /**
     * Datos de usuario
     */
    public dataUser: any;
    // public dataUser: IUser;
    /**
     * Datos de usuario
     */
    public dataErrors: any;

    /**
     * Url de la ubucacion del storage
     */
    @Prop({ type: String, required: true})
    public urlStorage: string;

    public urlProfileImagePreview: string;

    /**
     * Constructor de la clase
     */
    constructor() {
        super();
        this.dataUser = {};
        this.dataErrors = {};
        this.urlProfileImagePreview = '';
    }

    created() {
        this.getProfile();
    }

    /**
     * Evento de asignacion de archivo
     *
     * @author Jhoan Sebastian Chilito S. - Abr. 17 - 2020
     * @version 1.0.0
     *
     * @param event datos de evento
     * @param fieldName nombre de campo
     */
    public inputFile(event, fieldName: string): void {
        // Valida si selecciona un archivo para subir
        if (event.target.files[0]) {
            this.dataUser[fieldName] = event.target.files[0];
        }
    }

    /**
     * Obtiene los datos del usuario logueado
     *
     * @author Jhoan Sebastian Chilito S. - Ago. 11 - 2020
     * @version 1.0.0
     */
    public getProfile(): void {
         // Envia peticion para exportar datos de la tabla
        axios.get('/get-auth-profile')
        .then((res) => {
            let dataPayload = res.data.data ? jwtDecode(res.data.data) : null;

            const dataDecrypted = Object.assign({data:{}}, dataPayload);

            // Obtiene los datos del perfil
            this.dataUser = dataDecrypted?.data;
            this.urlProfileImagePreview = this.dataUser.profile_img_preview;

        })
        .catch((err) => {
            // console.log('Error al exportar los datos', err);
            this._pushNotification('Error al obtener los datos de perfil', false, 'Error');
        });
    }

    /**
     * Crea el formulario de datos para guardar
     *
     * @author Jhoan Sebastian Chilito S. - Jun. 26 - 2020
     * @version 1.0.0
     */
    public makeFormData(): FormData {
        let formData = new FormData();

        // Recorre los datos del formulario
        for (const key in this.dataUser) {
            if (this.dataUser.hasOwnProperty(key)) {
                const data = this.dataUser[key];
                // Valida si no es un objeto y si es un archivo
                if ( typeof data != 'object' || data instanceof File || data instanceof Date || data instanceof Array) {
                    // Valida si es un arreglo
                    if (Array.isArray(data)) {
                        data.forEach((element) => {
                            formData.append(`${key}[]`, element);
                        });
                    } else {
                        formData.append(key, data);
                    }
                }
            }
        }
        return formData;
    }

    /**
     * Guarda los datos de actualizacion de usuario
     *
     * @author Jhoan Sebastian Chilito S. - Ago. 10 - 2020
     * @version 1.0.0
     */
    public save() {
        const formData: FormData = this.makeFormData();
        formData.append('_method', 'put');

        // Envia peticion de guardado de datos
        axios.post(`/profile/${this.dataUser.id}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } })
        .then((res) => {
            let dataPayload = res.data.data ? jwtDecode(res.data.data) : null;
            const dataDecrypted = Object.assign({data:{}}, dataPayload);

            // Actualiza elemento modificado en la lista
            Object.assign(this.dataUser, dataDecrypted.data);
            // Emite notificacion de actualizacion de datos
            this._pushNotification(res.data.message);
        })
        .catch((err) => {
            // Emite notificacion de almacenamiento de datos
            this._pushNotification('Error al guardar los datos', false, 'Error');
            // Valida si hay errores asociados al formulario
            if (err.response.data.errors) {
                this.dataErrors = err.response.data.errors;
            }
        });
    }

    /**
     * Visualiza notificacion por accion
     *
     * @author Jhoan Sebastian Chilito S. - May. 09 - 2020
     * @version 1.0.0
     *
     * @param message mesaje de notificacion
     * @param isPositive valida si la notificacion debe ser posiva o negativa
     * @param title titulo de notificacion
     */
    private _pushNotification(message: string, isPositive: boolean = true, title: string = 'OK'): void {

        // Datos de notificacion (Por defecto guardar)
        const toastOptions = {
            closeButton: true,
            closeMethod: 'fadeOut',
            timeOut: 3000,
            tapToDismiss: false
        };
        // Valida el tipo de toast que se debe visualiza
        if (isPositive) {
            // Visualiza toast positivo
            toastr.success(message, title, toastOptions);
        } else {
            toastOptions.timeOut = 0;
            // Visualiza toast negativo
            toastr.error(message, title, toastOptions);
        }
    }
}
</script>
