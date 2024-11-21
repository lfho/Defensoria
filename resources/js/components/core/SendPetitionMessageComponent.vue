<template>
    <div>

    </div>
</template>
<script lang="ts">
 import { Component, Prop, Vue } from "vue-property-decorator";

   import axios from "axios";
   import { jwtDecode } from 'jwt-decode';

    /**
   * Componente para enviar peticiones al servidor con swal alert
   *
   * @author Nicolas Dario Ortiz Peña. - Agosto. 03 - 2021
   * @version 1.0.0
   */
   @Component
   export default class SendPetitionMessageComponent extends Vue {

    //titulo de la ventaana sweet alert
    @Prop({type: String, required: true })
     public titleAlert: string;

    //texto de la ventaana sweet alert
    @Prop({type: String, required: true })
     public textAlert: string;

     //ruta de la peticion al servidor
    @Prop({type: String, required: true })
     public resourceAlert: string;


      //id del objeto
    @Prop({type: Number })
     public objectId: number;


    //titulo de la ventaana de cuando se confirma el sweeralert
    @Prop({type: String, required: true })
     public titleConfirmationSwal: string;

    //texto de la ventaana de cuando se confirma el sweeralert
    @Prop({type: String, required: true })
     public textConfirmationSwal: string;


      /**
       * Constructor de la clase
       *
       * @author Nicolas Dario Ortiz Peña. - Agosto. 03 - 2021
       * @version 1.0.0
       */
      constructor() {
         super();

      }

        /**
       * envia los datos del al servidor y confirma con un sweeralert2
       *
       * @author Nicolas Dario Ortiz Peña. - Agosto. 03 - 2021
       * @version 1.0.0
       */
      public getPetition(): void {
                this.$swal({
                title: this.titleAlert,
                text: this.textAlert,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText:'No'
            }).then((result) => {
            if (result.value) {
                    const params={
                        id: this.objectId
                    }
                    //enviar peticion al servidor
                    axios.get(`${this.resourceAlert}`)
                    .then(res=>{

                        let dataPayload = res.data.data ? jwtDecode(res.data.data) : null;

                        const dataDecrypted = Object.assign({data:{id:null}}, dataPayload);

                        const request = dataDecrypted?.data;

                      // Actualiza elemento modificado en la lista
                       Object.assign((this.$parent as any)._findElementById

                       (request.id, false), request);
                        this.$swal({
                        title: this.titleConfirmationSwal,
                        text: this.textConfirmationSwal,
                        icon:'success'
                    })
                    })
                    .catch(error=>{
                        this.$swal({
                        title: 'Error al enviar',
                        text:'Error al enviar la solicitud' + error,
                        icon:'error'
                        })
                    })

                }
            })

      }
}
</script>
