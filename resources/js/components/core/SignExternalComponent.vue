<template>
  <div class="container">

    <div id="validar" v-if="accionVerContenedor === 'validarCodigo' || accionVerContenedor === ''">
      <div class="form-group row m-b-15">
        <h1>Código de acceso al documento:</h1>
        <div class="col-md-9">
          <input type="text" class="form-control" v-model="codigoAccesoDocumento" required>
          <small class="form-text text-muted">Ingrese el código de acceso proporcionado en su correo electrónico para ver el documento.</small>

        </div>
      </div>

      <button @click="validarcodigo" class="btn btn-primary">
        <i class="fas fa-check"></i> Validar Código
      </button>

    </div>

    <div id="contenedorFirmar" v-if="accionVerContenedor === 'verContenedorFirma'">
      <!-- Título -->
      <h2>Proceso de firma electrónica</h2>
      <p>Por favor, lea el documento y si está de acuerdo, haga clic en "Firmar documento".</p>

      <!-- Botón para firmar el documento -->
      <button @click="abrirModal" class="btn btn-success">
        <i class="fas fa-signature"></i> Firmar documento
      </button>

      <!-- Modal para mostrar el componente para firmar -->
      <b-modal v-model="mostrarModal" title="Firma electrónica" @hide="cerrarModal" ok-title="Firmar Documento"
        cancel-title="Cancelar" @ok="firmarDocumento($event)" size="lg">
        <!-- Asignamos una referencia al componente sign-to-image -->
        <sign-to-image ref="signToImage"></sign-to-image>

        <div class="form-group row m-b-15">
              <!-- <div class="col-form-label col-md-3 required">Ingrese una observación</div> -->
              <div class="col-md-12">
              <!-- Editor de texto para el contenido -->
              <text-area-editor
                  :value="observaciones"
                  name-field="observaciones"
                  placeholder="Ingrese una observación"
                  :hide-modules="{
                  'bold': true,
                  'image': true,
                  'code': true,
                  'link': true
                  }"
              ></text-area-editor>
              </div>
          </div>
        <small>Al hacer clic en "Firmar documento", certifico que estoy de acuerdo con que esta firma digital es una
          representación electrónica válida de mi firma escrita a mano y que puede ser utilizada en documentos según sea
          necesario.</small>

      </b-modal>

         <!-- Modal para devolver-->
        <b-modal v-model="mostrarModalDevolver" title="Devolver Documento" @hide="cerrarModalDevolver" ok-title="Devolver Documento"
        cancel-title="Cancelar" @ok="devolverDocumento($event)" size="lg">

        <div class="form-group row m-b-15">
              <!-- <div class="col-form-label col-md-3 required">Ingrese una observación</div> -->
              <div class="col-md-12">
              <!-- Editor de texto para el contenido -->
              <text-area-editor
                  :value="observaciones"
                  name-field="observaciones"
                  placeholder='Ingrese una observación'
                  :hide-modules="{
                  'bold': true,
                  'image': true,
                  'code': true,
                  'link': true
                  }"
              ></text-area-editor>
              </div>
          </div>
        <small>Al hacer clic en 'Devolver documento', este será enviado de vuelta a la persona que lo envió para que realice las modificaciones necesarias. Por favor, ingresa una observación explicando el motivo de la devolución del documento.</small>

      </b-modal>


      <!-- Botón para devolver el documento -->
      <button @click="abrirModalDevolver" class="btn btn-danger">
        <i class="fas fa-undo-alt"></i> Devolver documento
      </button>
      <br />
      <!-- Visualizar el documento en un iframe -->
      <iframe v-if="urlDocument" :key="urlDocument" id="pdfViewer" class="abrir_animacion" width="100%" height="600"
        frameborder="0" :src="urlDocument"></iframe>
    </div>

    <div id="contenedorRevElaboracion" v-if="accionVerContenedor === 'verContenedorRevElaboracion'">
      <!-- Título -->
      <h2>Proceso de Edición del Contenido del Documento</h2>
      <p>Por favor, revise el documento y realice las modificaciones que considere pertinentes. Luego, haga clic en "Enviar Documento".</p>

      <!-- Botón para firmar el documento -->
      <button @click="abrirModal" class="btn btn-success">
        <i class="fas fa-paper-plane"></i> Enviar documento
      </button>

      <!-- Modal para mostrar el componente para firmar -->
      <b-modal v-model="mostrarModal" title="Enviar Documento" @hide="cerrarModal" ok-title="Enviar Documento"
        cancel-title="Cancelar" @ok="enviarDocumento($event)" size="lg">
        <!-- Asignamos una referencia al componente sign-to-image -->
        <div class="form-group row m-b-15">
              <!-- <div class="col-form-label col-md-3 required">Ingrese una observación</div> -->
              <div class="col-md-12">
              <!-- Editor de texto para el contenido -->
              <text-area-editor
                  :value="observaciones"
                  name-field="observaciones"
                  placeholder='Ingrese una observación'
                  :hide-modules="{
                  'bold': true,
                  'image': true,
                  'code': true,
                  'link': false
                  }"
              ></text-area-editor>
              </div>
          </div>
        <small>Por favor, revise el documento y realice las modificaciones necesarias antes de hacer clic en "Enviar Documento".</small>

      </b-modal>

         <!-- Modal para devolver-->
         <b-modal v-model="mostrarModalDevolver" title="Devolver Documento" @hide="cerrarModalDevolver" ok-title="Devolver Documento"
        cancel-title="Cancelar" @ok="devolverDocumento($event)" size="lg">

        <div class="form-group row m-b-15">
              <!-- <div class="col-form-label col-md-3 required">Ingrese una observación</div> -->
              <div class="col-md-12">
              <!-- Editor de texto para el contenido -->
              <text-area-editor
                  :value="observaciones"
                  name-field="observaciones"
                  placeholder='Ingrese una observación'
                  :hide-modules="{
                  'bold': true,
                  'image': true,
                  'code': true,
                  'link': true
                  }"
              ></text-area-editor>
              </div>
          </div>
        <small>Al hacer clic en 'Devolver documento', este será enviado de vuelta a la persona que lo envió para que realice las modificaciones necesarias. Por favor, ingresa una observación explicando el motivo de la devolución del documento.</small>

      </b-modal>


        <!-- Botón para devolver el documento -->
        <button @click="abrirModalDevolver" class="btn btn-danger">
            <i class="fas fa-undo-alt"></i> Devolver documento
        </button>
        <br />
        <!-- Visualizar el documento en un iframe -->
        <iframe v-if="urlDocument" :key="urlDocument" id="pdfViewer" class="abrir_animacion" width="100%" height="500"
        frameborder="0" :src="urlDocument" @load="cargaCompletaIframe" ></iframe>
        <!-- Indicador de que el documento del iframe esta cargando su contenido -->
        <div v-if="!iframeCargado" style="position: absolute; left: 43%; top: 50%; font-size: 15px;">
            <div class="spinner" style="margin-top: 10px; width: 30px; height: 30px;"></div>
            <p>Cargando documento...</p>
        </div>

    </div>


    <div id="contenedorPublico" v-if="accionVerContenedor === 'verContenedorPublico'">
        <!-- Título -->
        <h2>Documento</h2>
        <!-- Visualizar el documento en un iframe -->
        <iframe v-if="urlDocument" :key="urlDocument" id="pdfViewer" class="abrir_animacion" width="100%" height="500"
          frameborder="0" :src="urlDocument"></iframe>
      </div>

      <div class="panel" v-if="urlAnexos && urlAnexos.length > 0">
        <div class="panel-heading">
          <div class="panel-title">
            <strong><h4>Anexos del documento</h4></strong>
          </div>
        </div>
        <div class="panel-body">
          <div class="" v-for="(anexo, key) in urlAnexos" :key="key">
            <viewer-attachement :ref="key" :list="anexo" :key="key" name="Anexo"></viewer-attachement>
          </div>
        </div>
      </div>
  </div>

</template>


<script lang="ts">
import axios from 'axios';
import {jwtDecode} from 'jwt-decode';

export default {
  name: 'SignExternalComponent',
  props: {
    // Propiedad para la URL del documento

    executeUrlAxios: {
      type: String,
      required: true
    },
    executeUrlAxiosEnviar: {
      type: String,
      required: true
    },
    idTablaFirmar: {
      type: String,
      required: false
    },
    executeUrlAxiosValidar: {
      type: String,
      required: true
    },
    iframeCargado: {
      type: Boolean,
      required: false,
      default: false
    },

  },
  data() {
    return {
      // Variable para controlar la apertura del modal
      mostrarModal: false,
      mostrarModalDevolver:false,
      codigoAccesoDocumento: '',
      urlDocument : '',
      urlAnexos : '',
      accionVerContenedor:'validarCodigo',
      // accionVerContenedor:'verContenedorFirma',
      idDocumento:'',
      observaciones:{}

    };
  },
  methods: {
    // Método para abrir el modal
    abrirModal() {
      this.mostrarModal = true;
    },
    // Método para cerrar el modal
    cerrarModal() {
      this.mostrarModal = false;
    },
    abrirModalDevolver() {
      this.mostrarModalDevolver = true;
    },
    // Método para cerrar el modal
    cerrarModalDevolver() {
      this.mostrarModalDevolver = false;
    },
    async enviarDocumento() {
      try {
        this.showLoadingGif("Enviando documento");

          const formData = new FormData();
          formData.append('id_documento', this.idDocumento);
          formData.append('tipo_firmado', 'externo');
          formData.append('_method', 'put');
          formData.append('accion_documento', 'Revisión (Editado por externo)');
          formData.append('observaciones', this.observaciones.observaciones ?? "");
          formData.append('idTablaFirmar', this.idTablaFirmar);

          axios.post(this.executeUrlAxiosEnviar, formData, {
            headers: {
              'Content-Type': 'multipart/form-data' // Opcional si no está definido por defecto
            }
          })
            .then((response) => {
              (this.$swal as any).close();

              this.$swal({
                icon: 'success',
                title: '¡Documento enviado exitosamente!',
                text: 'El documento fue enviado.',
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
              });

              this.accionVerContenedor = 'validarCodigo';
            })
            .catch((error) => {
              (this.$swal as any).close();

              console.error('Error');
              console.error(error.response.data);
            });
      } catch (error) {
        console.error("Error", error);
        // Maneja el error si es necesario
      }
    },
    // Método para devolver el documento
    async devolverDocumento() {
      try {
        this.showLoadingGif("Devolviendo documento");

          const formData = new FormData();
          formData.append('id_documento', this.idDocumento);
          formData.append('tipo_firmado', 'externo');
          formData.append('_method', 'put');
          formData.append('accion_documento', 'Devolver para modificaciones');
          formData.append('observaciones', this.observaciones.observaciones ?? "");
          formData.append('idTablaFirmar', this.idTablaFirmar);

          axios.post(this.executeUrlAxios, formData, {
            headers: {
              'Content-Type': 'multipart/form-data' // Opcional si no está definido por defecto
            }
          })
            .then((response) => {
              (this.$swal as any).close();

              this.$swal({
                icon: 'success',
                title: '¡Documento Devuelto exitosamente!',
                text: 'El documento fue devuelto.',
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
              });

              this.accionVerContenedor = 'validarCodigo';
            })
            .catch((error) => {
              (this.$swal as any).close();

              console.error('Error');
              console.error(error.response.data);
            });
      } catch (error) {
        console.error("Error", error);
        // Maneja el error si es necesario
      }
    },
    validarcodigo() {

      if(this.codigoAccesoDocumento) {

          try {
            this.showLoadingGif("Validando codigo");

            const formData = new FormData();
            formData.append('codigoAccesoDocumento', this.codigoAccesoDocumento);
            formData.append('idTablaFirmar', this.idTablaFirmar);


            axios.post(this.executeUrlAxiosValidar, formData, {
              headers: {
                'Content-Type': 'multipart/form-data' // Opcional si no está definido por defecto
              }
            })
              .then((res) => {
                (this.$swal as any).close();

                let dataPayload = res.data.data ? jwtDecode(res.data.data) : null;

                if(dataPayload['data']['success']) {
                    // Si el documento ya fue firmado por el usuario, ingresa a la siguiente condición
                    if(dataPayload['data']['rutapdf'] == 'firmado') {

                        (this.$swal as any).close();
                        // Muestra mensaje al usuario indicando que el documento ya fue firmado
                        this.$swal({
                            icon: 'info',
                            title: 'Documento no disponible',
                            text: 'El documento asociado a este código de acceso ya ha sido firmado. Si necesitas acceso, por favor comunícate con el administrador del sistema. Detalles de la firma: ' + dataPayload['data']['id_documento'],
                            confirmButtonText: "Aceptar",
                            allowOutsideClick: false,
                        });
                    } else if(dataPayload['data']['rutapdf'] == 'devuelto') { // Si el documento fue devuelto, se cumple esta condición

                        (this.$swal as any).close();
                        // Muestra mensaje al usuario indicando que el documento fue devuelto
                        this.$swal({
                            icon: 'info',
                            title: 'Documento no disponible',
                            text: 'El documento asociado a este código de acceso ha sido devuelto. Si necesitas acceso, por favor comunícate con el administrador del sistema.',
                            confirmButtonText: "Aceptar",
                            allowOutsideClick: false,
                        });
                    }

                    else if(dataPayload['data']['rutapdf'] == 'estado_diferente'){

                    (this.$swal as any).close();

                      this.$swal({
                        icon: 'info',
                        title: 'Documento no disponible',
                        text: dataPayload['data']['id_documento'],
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false,
                      });

                    }
                    else if(dataPayload['data']['rutapdf'] == 'publico'){

                      (this.$swal as any).close();

                      this.urlDocument = dataPayload['data']['id_documento'];
                      this.urlAnexos = dataPayload['data']['anexos'];
                      this.accionVerContenedor = 'verContenedorPublico';

                    }
                    else {

                        this.urlDocument = dataPayload['data']['rutapdf'];
                        this.urlAnexos = dataPayload['data']['anexos'];
                        this.idDocumento = dataPayload['data']['id_documento'];

                        if(this.urlDocument.includes("docs.google.com")){

                          this.accionVerContenedor = 'verContenedorRevElaboracion';

                        }else{
                          this.accionVerContenedor = 'verContenedorFirma';
                        }
                    }
                } else {

                    (this.$swal as any).close();

                    this.$swal({
                        icon: 'info',
                        title: 'Código ingresado incorrecto.',
                        text: 'El código ingresado es incorrecto. Por favor, revise y vuelva a intentarlo.',
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false,
                    });
                }

            })
            .catch((error) => {
                (this.$swal as any).close();

                console.error('Error');
                console.error(error.response.data);
            });


          } catch (error) {
            (this.$swal as any).close();

            console.error("Error", error);
            // Maneja el error si es necesario
          }
      }else{

        this.$swal({
          icon: 'info',
          title: 'Ingrese un código de acceso.',
          text: 'Por favor, ingrese un código válido.',
          confirmButtonText: "Aceptar",
          allowOutsideClick: false,
        });


      }

    },
    showLoadingGif(mensaje) {
      this.$swal({
        html: '<img src="/assets/img/loadingintraweb.gif" alt="Cargando..." style="width: 100px;"><br><span>' + mensaje + '.</span>',
        showCancelButton: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
      });
    },
    // Método para guardar la imagen al firmar
    async firmarDocumento() {
      try {
        this.showLoadingGif("Firmando documento");

        let rutaImagen = "";
        if(this.$refs.signToImage.mostrarDibujar){

            // Llama a la función guardarImagen del componente sign-to-image
            rutaImagen = await this.$refs.signToImage.guardarImagen();
            // Valida si la imagen esta vacía, no dibujó nada en el cuadro
            if(rutaImagen == "imagen_vacia") {
                // Cierra el modal o mensaje de alerta actual (si está abierto)
                (this.$swal as any).close();
                // Muestra un nuevo mensaje de alerta
                this.$swal({
                    icon: 'warning',
                    html: 'No se detectó ninguna firma en el área de dibujo. Asegúrese de dibujar su firma o cargar una imagen para que podamos procesar su documento correctamente.',
                    confirmButtonText: "Aceptar",
                    allowOutsideClick: false,
                });
                // Detiene la ejecución de la función actual
                return;
            }
        }else{

          rutaImagen = this.$refs.signToImage.firmaSrc.attached;

        }

        if(rutaImagen){

          const formData = new FormData();
          formData.append('rutafirma', rutaImagen);
          formData.append('urldocument', this.urlDocument);
          formData.append('id_documento', this.idDocumento);
          formData.append('tipo_firmado', 'externo');
          formData.append('_method', 'put');
          formData.append('accion_documento', 'firmar');
          formData.append('observaciones', this.observaciones.observaciones ?? "");
          formData.append('idTablaFirmar', this.idTablaFirmar);

          axios.post(this.executeUrlAxios, formData, {
            headers: {
              'Content-Type': 'multipart/form-data' // Opcional si no está definido por defecto
            }
          })
            .then((response) => {
              (this.$swal as any).close();

              this.$swal({
                icon: 'success',
                title: '¡Documento firmado exitosamente!',
                text: 'Gracias por firmar. Recibirá una copia del documento en su correo una vez que todos los participantes hayan firmado.',
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
              });

              this.accionVerContenedor = 'validarCodigo';


            })
            .catch((error) => {
              (this.$swal as any).close();

              console.error('Error');
              console.error(error.response.data);
            });

        }else{

              this.$swal({
                    icon: 'error',
                    html: 'Error en la firma',
                    confirmButtonText: "Aceptar",
                    allowOutsideClick: false,
                });
                (this.$swal as any).close();

        }



      } catch (error) {
        console.error("Error", error);
        // Maneja el error si es necesario
      }
    },
    // Función que se invoca e indica que el iframe ha sido cargado completamente, esto solo para los documento diferentes de pdf
    cargaCompletaIframe() {
        this.iframeCargado = true;
    }
  }
};
</script>
