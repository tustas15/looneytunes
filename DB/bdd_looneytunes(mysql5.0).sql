/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     3/9/2024 10:26:27                            */
/*==============================================================*/


drop table if exists TAB_ADMINISTADORES;

drop table if exists TAB_BANCOS;

drop table if exists TAB_CATEGORIAS;

drop table if exists TAB_CATEGORIA_DEPORTISTA;

drop table if exists TAB_DEPORTISTAS;

drop table if exists TAB_DETALLES;

drop table if exists TAB_ENTRENADORES;

drop table if exists TAB_ENTRENADOR_CATEGORIA;

drop table if exists TAB_ESTADO_PAGOS;

drop table if exists TAB_FOTOS_USUARIO;

drop table if exists TAB_INFORMES;

drop table if exists TAB_LOGS;

drop table if exists TAB_PAGOS;

drop table if exists TAB_PDFS;

drop table if exists TAB_PRODUCTOS;

drop table if exists TAB_PRODUCTOS_CATEGORIA;

drop table if exists TAB_REPRESENTANTES;

drop table if exists TAB_REPRESENTANTES_DEPORTISTAS;

drop table if exists TAB_TEMP_DEPORTISTAS;

drop table if exists TAB_TIPO_USUARIO;

drop table if exists TAB_USUARIOS;

drop table if exists TAB_USU_TIPO;

/*==============================================================*/
/* Table: TAB_ADMINISTADORES                                    */
/*==============================================================*/
create table TAB_ADMINISTADORES
(
   ID_ADMINISTRADOR     int not null,
   ID_USUARIO           int,
   NOMBRE_ADMIN         varchar(50),
   APELLIDO_ADMIN       varchar(50),
   CELULAR_ADMIN        varchar(10),
   primary key (ID_ADMINISTRADOR)
);

/*==============================================================*/
/* Table: TAB_BANCOS                                            */
/*==============================================================*/
create table TAB_BANCOS
(
   ID_BANCO             int not null,
   NOMBRE               varchar(100),
   ESTADO               enum('activo','inactivo'),
   primary key (ID_BANCO)
);

/*==============================================================*/
/* Table: TAB_CATEGORIAS                                        */
/*==============================================================*/
create table TAB_CATEGORIAS
(
   ID_CATEGORIA         int not null,
   CATEGORIA            varchar(30),
   primary key (ID_CATEGORIA)
);

/*==============================================================*/
/* Table: TAB_CATEGORIA_DEPORTISTA                              */
/*==============================================================*/
create table TAB_CATEGORIA_DEPORTISTA
(
   ID_CATEGORIA         int,
   ID_DEPORTISTA        int
);

/*==============================================================*/
/* Table: TAB_DEPORTISTAS                                       */
/*==============================================================*/
create table TAB_DEPORTISTAS
(
   ID_DEPORTISTA        int not null,
   ID_USUARIO           int,
   ID_CATEGORIA         int,
   ID_TEMP_DEPORTISTAS  int,
   NOMBRE_DEPO          varchar(50),
   APELLIDO_DEPO        varchar(50),
   FECHA_NACIMIENTO     date,
   CEDULA_DEPO          varchar(10),
   NUMERO_CELULAR       varchar(10),
   GENERO               varchar(20),
   primary key (ID_DEPORTISTA)
);

/*==============================================================*/
/* Table: TAB_DETALLES                                          */
/*==============================================================*/
create table TAB_DETALLES
(
   ID_DETALLE           int not null,
   ID_DEPORTISTA        int,
   ID_USUARIO           int,
   NUMERO_CAMISA        varchar(2),
   ALTURA               varchar(10),
   PESO                 varchar(10),
   FECHA_INGRESO        date,
   primary key (ID_DETALLE)
);

/*==============================================================*/
/* Table: TAB_ENTRENADORES                                      */
/*==============================================================*/
create table TAB_ENTRENADORES
(
   ID_ENTRENADOR        int not null,
   ID_USUARIO           int,
   NOMBRE_ENTRE         varchar(50),
   APELLIDO_ENTRE       varchar(50),
   EXPERIENCIA_ENTRE    varchar(10),
   CELULAR_ENTRE        varchar(10),
   CORREO_ENTRE         varchar(50),
   DIRECCION_ENTRE      varchar(50),
   CEDULA_ENTRE         varchar(10),
   primary key (ID_ENTRENADOR)
);

/*==============================================================*/
/* Table: TAB_ENTRENADOR_CATEGORIA                              */
/*==============================================================*/
create table TAB_ENTRENADOR_CATEGORIA
(
   ID_ENTRENADOR        int,
   ID_CATEGORIA         int
);

/*==============================================================*/
/* Table: TAB_ESTADO_PAGOS                                      */
/*==============================================================*/
create table TAB_ESTADO_PAGOS
(
   ID_ESTADO            int not null,
   ID_PAGO              int,
   ID_CATEGORIA         int,
   ID_DEPORTISTA        int,
   FECHA                date,
   ESTADO               enum('pagado','atrasado','pago'),
   primary key (ID_ESTADO)
);

/*==============================================================*/
/* Table: TAB_FOTOS_USUARIO                                     */
/*==============================================================*/
create table TAB_FOTOS_USUARIO
(
   ID_FOTO              int not null,
   ID_TIPO              int,
   FOTO                 longblob,
   primary key (ID_FOTO)
);

/*==============================================================*/
/* Table: TAB_INFORMES                                          */
/*==============================================================*/
create table TAB_INFORMES
(
   ID_REPRESENTANTE     int,
   ID_DEPORTISTA        int,
   ID_ENTRENADOR        int,
   ID_INFORME           int,
   INFORME              text,
   FECHA_CREACION       time
);

/*==============================================================*/
/* Table: TAB_LOGS                                              */
/*==============================================================*/
create table TAB_LOGS
(
   ID_LOG               int not null,
   ID_USUARIO           int,
   EVENTO               text,
   HORA_LOG             time,
   DIA_LOG              date,
   IP                   varchar(20),
   TIPO_EVENTO          enum('inicio_sesion','cierre_sesion','nuevo_usuario','subida_base_datos','nuevo_producto_creado','nueva_categoria_producto_creado','nueva_categoria_deportista_creado','nuevo_observacion_enviada','nuevo_pago_agregado','nuevo_limite_categoria_deportistas_definido','usuario_inactivo','usuario_activo','actualizacion_perfil','categoria_deportista_eliminado','nuevo_observacion_eliminada','nuevo_dato_creado','dato_eliminado','subida_pdf','descarga_pdf','cambio_contraseña','pago_eliminado','error_sistema','archivo_adjunto_subido','usuario_bloqueado','usuario_desbloqueado'),
   primary key (ID_LOG)
);

/*==============================================================*/
/* Table: TAB_PAGOS                                             */
/*==============================================================*/
create table TAB_PAGOS
(
   ID_PAGO              int not null,
   ID_REPRESENTANTE     int,
   ID_DEPORTISTA        int,
   ID_BANCO             int,
   METODO_PAGO          varchar(50),
   MONTO                decimal(10,2),
   FECHA_PAGO           date,
   MOTIVO               text,
   ENITDAD_ORIGEN       varchar(100),
   REGISTRADO_POR       enum('admin','repre','depo'),
   primary key (ID_PAGO)
);

/*==============================================================*/
/* Table: TAB_PDFS                                              */
/*==============================================================*/
create table TAB_PDFS
(
   ID_PDF               int not null,
   ID_USUARIO           int,
   FILE_NAME            varchar(255),
   FILE_PATH            varchar(255),
   UPLOADED_AT          timestamp,
   primary key (ID_PDF)
);

/*==============================================================*/
/* Table: TAB_PRODUCTOS                                         */
/*==============================================================*/
create table TAB_PRODUCTOS
(
   ID_PRODUCTO          int not null,
   ID_USUARIO           int,
   ID_CATEGORIA_PRODUCTO int,
   PRODUCTO_CODIGO      varchar(70),
   PRODUCTO_NOMBRE      varchar(70),
   PRODUCTO_PRECIO      decimal(30,2),
   PRODUCTO_STOCK       int(25),
   PRODUCTO_FOTO        varchar(500),
   primary key (ID_PRODUCTO)
);

/*==============================================================*/
/* Table: TAB_PRODUCTOS_CATEGORIA                               */
/*==============================================================*/
create table TAB_PRODUCTOS_CATEGORIA
(
   ID_CATEGORIA_PRODUCTO int not null,
   CATEGORIA_NOMBRE     varchar(50),
   CATEGORIA_UBICACION  varchar(150),
   primary key (ID_CATEGORIA_PRODUCTO)
);

/*==============================================================*/
/* Table: TAB_REPRESENTANTES                                    */
/*==============================================================*/
create table TAB_REPRESENTANTES
(
   ID_REPRESENTANTE     int not null,
   ID_USUARIO           int,
   NOMBRE_REPRE         varchar(50),
   APELLIDO_REPRE       varchar(50),
   CELULAR_REPRE        varchar(10),
   CORREO_REPRE         varchar(100),
   DIRECCION_REPRE      varchar(100),
   CEDULA_REPRE         varchar(10),
   primary key (ID_REPRESENTANTE)
);

/*==============================================================*/
/* Table: TAB_REPRESENTANTES_DEPORTISTAS                        */
/*==============================================================*/
create table TAB_REPRESENTANTES_DEPORTISTAS
(
   ID                   int not null,
   ID_DEPORTISTA        int,
   ID_REPRESENTANTE     int,
   primary key (ID)
);

/*==============================================================*/
/* Table: TAB_TEMP_DEPORTISTAS                                  */
/*==============================================================*/
create table TAB_TEMP_DEPORTISTAS
(
   ID_TEMP_DEPORTISTAS  int not null,
   ID_USUARIO           int,
   primary key (ID_TEMP_DEPORTISTAS)
);

/*==============================================================*/
/* Table: TAB_TIPO_USUARIO                                      */
/*==============================================================*/
create table TAB_TIPO_USUARIO
(
   ID_TIPO              int not null,
   ID_USU_TIPO          int,
   TIPO                 varchar(20),
   primary key (ID_TIPO)
);

/*==============================================================*/
/* Table: TAB_USUARIOS                                          */
/*==============================================================*/
create table TAB_USUARIOS
(
   ID_USUARIO           int not null,
   ID_USU_TIPO          int,
   USUARIO              varchar(20),
   PASS                 varchar(100),
   INTENTOS_FALLIDOS_   int(11),
   BLOQUEADO_HASTA      date,
   primary key (ID_USUARIO)
);

/*==============================================================*/
/* Table: TAB_USU_TIPO                                          */
/*==============================================================*/
create table TAB_USU_TIPO
(
   ID_USU_TIPO          int not null,
   primary key (ID_USU_TIPO)
);

alter table TAB_ADMINISTADORES add constraint FK_REFERENCE_3 foreign key (ID_USUARIO)
      references TAB_USUARIOS (ID_USUARIO) on delete restrict on update restrict;

alter table TAB_CATEGORIA_DEPORTISTA add constraint FK_REFERENCE_23 foreign key (ID_CATEGORIA)
      references TAB_CATEGORIAS (ID_CATEGORIA) on delete restrict on update restrict;

alter table TAB_CATEGORIA_DEPORTISTA add constraint FK_REFERENCE_24 foreign key (ID_DEPORTISTA)
      references TAB_DEPORTISTAS (ID_DEPORTISTA) on delete restrict on update restrict;

alter table TAB_DEPORTISTAS add constraint FK_REFERENCE_13 foreign key (ID_TEMP_DEPORTISTAS)
      references TAB_TEMP_DEPORTISTAS (ID_TEMP_DEPORTISTAS) on delete restrict on update restrict;

alter table TAB_DEPORTISTAS add constraint FK_REFERENCE_4 foreign key (ID_USUARIO)
      references TAB_USUARIOS (ID_USUARIO) on delete restrict on update restrict;

alter table TAB_DEPORTISTAS add constraint FK_REFERENCE_5 foreign key (ID_CATEGORIA)
      references TAB_CATEGORIAS (ID_CATEGORIA) on delete restrict on update restrict;

alter table TAB_DETALLES add constraint FK_REFERENCE_14 foreign key (ID_DEPORTISTA)
      references TAB_DEPORTISTAS (ID_DEPORTISTA) on delete restrict on update restrict;

alter table TAB_DETALLES add constraint FK_REFERENCE_15 foreign key (ID_USUARIO)
      references TAB_USUARIOS (ID_USUARIO) on delete restrict on update restrict;

alter table TAB_ENTRENADORES add constraint FK_REFERENCE_6 foreign key (ID_USUARIO)
      references TAB_USUARIOS (ID_USUARIO) on delete restrict on update restrict;

alter table TAB_ENTRENADOR_CATEGORIA add constraint FK_REFERENCE_18 foreign key (ID_ENTRENADOR)
      references TAB_ENTRENADORES (ID_ENTRENADOR) on delete restrict on update restrict;

alter table TAB_ENTRENADOR_CATEGORIA add constraint FK_REFERENCE_19 foreign key (ID_CATEGORIA)
      references TAB_CATEGORIAS (ID_CATEGORIA) on delete restrict on update restrict;

alter table TAB_ESTADO_PAGOS add constraint FK_REFERENCE_32 foreign key (ID_PAGO)
      references TAB_PAGOS (ID_PAGO) on delete restrict on update restrict;

alter table TAB_ESTADO_PAGOS add constraint FK_REFERENCE_33 foreign key (ID_CATEGORIA)
      references TAB_CATEGORIAS (ID_CATEGORIA) on delete restrict on update restrict;

alter table TAB_ESTADO_PAGOS add constraint FK_REFERENCE_34 foreign key (ID_DEPORTISTA)
      references TAB_DEPORTISTAS (ID_DEPORTISTA) on delete restrict on update restrict;

alter table TAB_FOTOS_USUARIO add constraint FK_REFERENCE_28 foreign key (ID_TIPO)
      references TAB_TIPO_USUARIO (ID_TIPO) on delete restrict on update restrict;

alter table TAB_INFORMES add constraint FK_REFERENCE_25 foreign key (ID_REPRESENTANTE)
      references TAB_REPRESENTANTES (ID_REPRESENTANTE) on delete restrict on update restrict;

alter table TAB_INFORMES add constraint FK_REFERENCE_26 foreign key (ID_DEPORTISTA)
      references TAB_DEPORTISTAS (ID_DEPORTISTA) on delete restrict on update restrict;

alter table TAB_INFORMES add constraint FK_REFERENCE_27 foreign key (ID_ENTRENADOR)
      references TAB_ENTRENADORES (ID_ENTRENADOR) on delete restrict on update restrict;

alter table TAB_LOGS add constraint FK_REFERENCE_29 foreign key (ID_USUARIO)
      references TAB_USUARIOS (ID_USUARIO) on delete restrict on update restrict;

alter table TAB_PAGOS add constraint FK_REFERENCE_31 foreign key (ID_BANCO)
      references TAB_BANCOS (ID_BANCO) on delete restrict on update restrict;

alter table TAB_PAGOS add constraint FK_REFERENCE_8 foreign key (ID_REPRESENTANTE)
      references TAB_REPRESENTANTES (ID_REPRESENTANTE) on delete restrict on update restrict;

alter table TAB_PAGOS add constraint FK_REFERENCE_9 foreign key (ID_DEPORTISTA)
      references TAB_DEPORTISTAS (ID_DEPORTISTA) on delete restrict on update restrict;

alter table TAB_PDFS add constraint FK_REFERENCE_30 foreign key (ID_USUARIO)
      references TAB_USUARIOS (ID_USUARIO) on delete restrict on update restrict;

alter table TAB_PRODUCTOS add constraint FK_REFERENCE_21 foreign key (ID_USUARIO)
      references TAB_USUARIOS (ID_USUARIO) on delete restrict on update restrict;

alter table TAB_PRODUCTOS add constraint FK_REFERENCE_22 foreign key (ID_CATEGORIA_PRODUCTO)
      references TAB_PRODUCTOS_CATEGORIA (ID_CATEGORIA_PRODUCTO) on delete restrict on update restrict;

alter table TAB_REPRESENTANTES add constraint FK_REFERENCE_7 foreign key (ID_USUARIO)
      references TAB_USUARIOS (ID_USUARIO) on delete restrict on update restrict;

alter table TAB_REPRESENTANTES_DEPORTISTAS add constraint FK_REFERENCE_10 foreign key (ID_DEPORTISTA)
      references TAB_DEPORTISTAS (ID_DEPORTISTA) on delete restrict on update restrict;

alter table TAB_REPRESENTANTES_DEPORTISTAS add constraint FK_REFERENCE_11 foreign key (ID_REPRESENTANTE)
      references TAB_REPRESENTANTES (ID_REPRESENTANTE) on delete restrict on update restrict;

alter table TAB_TEMP_DEPORTISTAS add constraint FK_REFERENCE_12 foreign key (ID_USUARIO)
      references TAB_USUARIOS (ID_USUARIO) on delete restrict on update restrict;

alter table TAB_TIPO_USUARIO add constraint FK_REFERENCE_2 foreign key (ID_USU_TIPO)
      references TAB_USU_TIPO (ID_USU_TIPO) on delete restrict on update restrict;

alter table TAB_USUARIOS add constraint FK_REFERENCE_1 foreign key (ID_USU_TIPO)
      references TAB_USU_TIPO (ID_USU_TIPO) on delete restrict on update restrict;

