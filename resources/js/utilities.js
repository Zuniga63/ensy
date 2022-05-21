/**
 * @fileoverview Libreria con funciones de utilidad
 *
 * @author Zuniga63
 * @version 0.1
 */

/**
 *  Se encarga de formatear el numero a moneda
 * @param {string} number a formatear.
 */
export function formatCurrency(number) {
  let fractionDigits = 0;
  let style = "currency";
  let currency = "COP";
  const formater = new Intl.NumberFormat("es-CO", {
    style,
    currency,
    minimumFractionDigits: fractionDigits,
  });
  return formater.format(number);
}

/**
 * Se encarga de poner un punto cada tercer numero
 * de un documento
 * @param {string} document numero de documento
 * @return {string}
 */
export function formatDocument(document) {
  if (document) {
    //Se recupera cada elemento de forma inversa
    /**@type {array} */
    let elements = document.split("").reverse();
    let result = [];

    elements.forEach((item, index) => {
      let count = index + 1;
      result.push(item);

      if (count % 3 === 0) result.push(".");
    });

    return result.reverse().join("");
  }
}

/**
 * Se encarga de dar un formato a un numero de la forma ### ### ####
 * @param {string} phone Numero de telefono a formatear
 */
export function formatPhone(phone) {
  if (phone) {
    //Se recupera cada caracter
    /**@type {array} */
    let elements = phone.split("").reverse();
    let result = [];

    elements.forEach((element, index) => {
      let count = index + 1;
      let count2 = count - 4;

      result.push(element);

      if(count === 4 || (count > 4 && count2 % 3 === 0)){
        result.push(" ");
      }
    });//.end forEach

    return result.reverse().join("");
  }
}

/**
 * Codigo que me permite seleccionar el texto de un elemento
 * tomado de: https://stackoverflow.com/questions/985272/selecting-text-in-an-element-akin-to-highlighting-with-your-mouse
 */
export function selectText(event) {
  let node = event.target;
  if (document.body.createTextRange) {
    const range = document.body.createTextRange();
    range.moveToElementText(node);
    range.select();
  } else if (window.getSelection) {
    const selection = window.getSelection();
    const range = document.createRange();
    range.selectNodeContents(node);
    selection.removeAllRanges();
    selection.addRange(range);
  } else {
    console.warn("Could not select text in node: Unsupported browser.");
  }
}

/**
 * Este metodo se encarga de llevar a minusculas el texto
 * pasado como parametro y remover de forma segura los guiños como
 * las eñes.
 * @param String text cadena de texto a normalizar.
 */
export function normalizeString(text) {
  return text
    ? text
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
    : null;
}
