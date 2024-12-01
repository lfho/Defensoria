<template>
    <div></div>
  </template>
  <script lang="ts">
  import { Component, Prop, Vue } from "vue-property-decorator";

  import axios from "axios";
  import {jwtDecode} from 'jwt-decode';

  /**
   * Componente para mostrar una alerta de confirmacion con peticion
   *
   * @author Kleverman Salazar Florez. - Sep. 22 - 2022
   * @version 1.0.0
   */
  @Component
  export default class AlertConfirmationComponent extends Vue {
    /**
     * Nombre del recurso
     */
    @Prop({ type: String, required: true })
    public nameResource: string;

    /**
     * Titulo del modal
     */
    @Prop({ type: String, required: true, default: "¿Esta seguro?" })
    public title: string;

    /**
     * Texto secundario del modal
     */
    @Prop({ type: String, required: false })
    public secondaryText: string;

    /**
     * Icono del modal
     */
    @Prop({ type: String, required: false, default: "question" })
    public icon: string;

    /**
     * Color del boton de confirmacion de la modal
     */
    @Prop({ type: String, required: false, default: "#3085d6" })
    public confirmButtonColor: string;

    /**
     * Color del boton de cancelacion de la modal
     */
    @Prop({ type: String, required: false, default: "#d33" })
    public cancelButtonColor: string;

    /**
     * Texto del boton de confirmacion
     */
    @Prop({ type: String, required: true, default: "Aceptar" })
    public confirmationText: string;

    /**
     * Texto del boton de cancelacion
     */
    @Prop({ type: String, required: true, default: "Cancelar" })
    public cancellationText: string;

    /**
     * indica si el modal tiene un text area que llegara al controlador
     */
     @Prop({ type: Boolean, default: false  })
    public textarea: boolean;

    /**
     * Titulo despues de un envio exitoso
     */
    @Prop({ type: String, required: false, default: "Envio exitoso" })
    public titleSuccessfulShipment: string;

    /**
     * Texto secundario despues de un envio exitoso
     */
    @Prop({ type: String, required: false })
    public secondaryTextSuccessfulShipment: string;

    /**
     * Titulo despues de un error en el envio
     */
    @Prop({ type: String, required: false })
    public titleErrorSending: string;

    /**
     * Texto secundario despues de un error en el envio
     */
    @Prop({ type: String, required: false, default:"Por favor verifique o comuníquese con soporte@seven.com.co" })
    public secondaryTextErrorSending: string;


        /**
     * Texto de cargando la petición
     */
     @Prop({ type: String, required: false, default:"Procesando solicitud, por favor espera..." })
    public loadingData: string;

    

    /**
     * Constructor de la clase
     *
     * @author Kleverman Salzar Florez. - Sep. 22 - 2022
     * @version 1.0.0
     */
    constructor() {
      super();
    }

    /**
     * Abre el modal de confirmacion para el envio de los datos
     *
     * @author Kleverman Salazar Florez. - Sep. 22 - 2022
     * @version 1.0.0
     */
    public openConfirmationModal(elementId:number): void {
      this.$swal({
        title: this.title,
        text: this.secondaryText,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: this.confirmButtonColor,
        cancelButtonColor: this.cancelButtonColor,
        confirmButtonText: this.confirmationText,
        cancelButtonText: this.cancellationText,
        ...(this.textarea ? { input: "textarea" } : {}),
      }).then((result) => {
        if (result.value) {
          const params = {
            id: elementId,
          };
          // Abre el swal de cargando
          this.$swal({
            title: "Cargando",
            html: this.loadingData,
            onBeforeOpen: () => {
              (this.$swal as any).showLoading();
            },
          });
          // Envia peticion al servidor
          axios
            // .get(`${this.nameResource}/${elementId}`)
            .get(`${this.nameResource}/${elementId}${result.value !== true ? `/${result.value}` : ''}`)
            .then((res) => {
              // Convertir el string a un objeto JavaScript
              let dataPayload = res.data.data ? jwtDecode(res.data.data) : null;

              const dataDecrypted = Object.assign({data:{id:null}}, dataPayload);
   
              // Actualiza elemento modificado en la lista
              Object.assign(
                        (this.$parent as any)._findElementById(
                          dataDecrypted?.data.id,
                            false
                        ),
                        dataDecrypted["data"]
                    );

              this.$swal({
                title: this.titleSuccessfulShipment,
                text: this.secondaryTextSuccessfulShipment,
                icon: "success",
              });
            })
            .catch((error) => {
              this.$swal({
                title: this.titleErrorSending,
                text: this.secondaryTextErrorSending,
                icon: "error",
              });
            });
        }

        if (this.textarea && result.value === "" ) {
          this.$swal({
                title: this.titleErrorSending,
                text: "¡Debe de ingresar una justificación valida!",
                icon: "error",
              });
        }
      });
    }
  }
  </script>
