-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 12-11-2018 a las 12:50:01
-- Versión del servidor: 5.7.23
-- Versión de PHP: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `multiagentes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `ip` varchar(100) NOT NULL,
  `puerto` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL AUTO_INCREMENT,
  `tiendaActual` int(11) DEFAULT NULL,
  PRIMARY KEY (`idcliente`),
  KEY `tiendaActual` (`tiendaActual`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientetienda`
--

DROP TABLE IF EXISTS `clientetienda`;
CREATE TABLE IF NOT EXISTS `clientetienda` (
  `idCliente` int(11) NOT NULL,
  `idTienda` int(11) NOT NULL,
  PRIMARY KEY (`idCliente`,`idTienda`),
  KEY `idTienda` (`idTienda`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`nombre`) VALUES
('producto1'),
('producto2'),
('producto4'),
('producto5');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

DROP TABLE IF EXISTS `stock`;
CREATE TABLE IF NOT EXISTS `stock` (
  `idtienda` int(11) NOT NULL,
  `idproducto` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL,
  PRIMARY KEY (`idtienda`,`idproducto`),
  KEY `idproducto` (`idproducto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `stock`
--

INSERT INTO `stock` (`idtienda`, `idproducto`, `cantidad`) VALUES
(1, 'producto1', -6),
(1, 'producto2', 2),
(1, 'producto4', 10),
(1, 'producto5', 10),
(100, 'producto1', -6),
(100, 'producto2', 0),
(100, 'producto4', 10),
(100, 'producto5', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tienda`
--

DROP TABLE IF EXISTS `tienda`;
CREATE TABLE IF NOT EXISTS `tienda` (
  `id` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tienda`
--

INSERT INTO `tienda` (`id`, `estado`) VALUES
(1, 1),
(100, 1);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`tiendaActual`) REFERENCES `tienda` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `clientetienda`
--
ALTER TABLE `clientetienda`
  ADD CONSTRAINT `clientetienda_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `clientetienda_ibfk_2` FOREIGN KEY (`idTienda`) REFERENCES `tienda` (`id`);

--
-- Filtros para la tabla `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`nombre`),
  ADD CONSTRAINT `stock_ibfk_2` FOREIGN KEY (`idtienda`) REFERENCES `tienda` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
