"""Cliente de las rutas web existentes; no modifica el código de la tienda."""
import getpass
import http.cookiejar
import json
import urllib.error
import urllib.parse
import urllib.request
from html.parser import HTMLParser

BASE = 'https://tienda.esremate.cl'


class Formulario(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.csrf = None
        self.selects = {}
        self.current = None
        self.option = None

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == 'input' and attrs.get('name') == '_token':
            self.csrf = attrs.get('value')
        if tag == 'select':
            self.current = attrs.get('name')
            if self.current:
                self.selects[self.current] = []
        if tag == 'option' and self.current:
            self.option = {'id': attrs.get('value', ''), 'nombre': ''}

    def handle_data(self, data):
        if self.option is not None:
            self.option['nombre'] += data

    def handle_endtag(self, tag):
        if tag == 'option' and self.option is not None:
            self.option['nombre'] = self.option['nombre'].strip()
            if self.option['id']:
                self.selects[self.current].append(self.option)
            self.option = None
        if tag == 'select':
            self.current = None


class Cliente:
    def __init__(self):
        # Cookies solamente en memoria. No se exportan credenciales ni sesiones.
        self.http = urllib.request.build_opener(
            urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))

    def request(self, path, data=None):
        url = BASE + path
        payload = urllib.parse.urlencode(data).encode() if data is not None else None
        request = urllib.request.Request(url, data=payload, headers={
            'User-Agent': 'CargaInfo/1.0', 'Referer': BASE + '/login',
        })
        with self.http.open(request, timeout=30) as response:
            if urllib.parse.urlsplit(response.url).netloc != urllib.parse.urlsplit(BASE).netloc:
                raise RuntimeError('La respuesta salió del dominio de la tienda.')
            return response.url, response.read().decode('utf-8', errors='replace')

    def opciones(self, email, password):
        _, html = self.request('/login')
        form = Formulario()
        form.feed(html)
        if not form.csrf:
            raise RuntimeError('No se encontró el token CSRF del acceso.')
        self.request('/login', {'_token': form.csrf, 'email': email, 'password': password})
        url, html = self.request('/admin/productos/crear')
        if urllib.parse.urlsplit(url).path.rstrip('/') != '/admin/productos/crear':
            raise RuntimeError('No se pudo acceder a la creación: revisa la cuenta y sus permisos.')
        form = Formulario()
        form.feed(html)
        if not form.csrf or not form.selects.get('category_id'):
            raise RuntimeError('La respuesta no contiene el formulario esperado.')
        return form.selects


if __name__ == '__main__':
    print('Conexión HTTP a ' + BASE)
    print('Esta prueba solo inicia sesión y consulta opciones; no crea productos.')
    email = input('Correo de acceso: ').strip()
    password = getpass.getpass('Contraseña (oculta, no se guarda): ')
    try:
        options = Cliente().opciones(email, password)
        print(json.dumps(options, ensure_ascii=False, indent=2))
        if not options.get('tienda_id'):
            print('No hay tiendas seleccionables. Crear un producto requiere una tienda existente.')
    except (urllib.error.URLError, RuntimeError) as error:
        print('No se completó la conexión: ' + str(error))
        raise SystemExit(1)
    finally:
        password = None
