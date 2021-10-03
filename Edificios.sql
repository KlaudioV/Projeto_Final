BEGIN TRANSACTION;
CREATE TABLE Edificios
(
    idEdificio  integer
        constraint Edificios_pk
            primary key autoincrement,
    idAutor     int     not null
        constraint Autor_pk
            unique,
    Localizacao varchar not null,
    Autores     varchar not null,
    Data        varchar not null,
    TipoEdif    varchar not null,
    idImag      int     not null,
    CoordLong   double  not null,
    CoordLat    double  not null
);
COMMIT;
