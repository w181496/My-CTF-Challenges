from flask import Flask, request, render_template, redirect, url_for, session, render_template_string
import random
import os

app = Flask(__name__)

def sanitize(str):
    return str.replace(".", "").replace("{{", "")

@app.route('/')
def home():
    return render_template('index.html')


@app.route('/ping')
def ping():
    return "pong"


@app.route('/flag')
def flag():
    return "🇹🇼"


@app.route('/koreafish')
def koreafish():
    try:
        id = request.args.get("id")
        return render_template('fish.html', id=id)
    except:
        return redirect("/error_page?err=no_id.html")


@app.route('/koreacat')
def cat():
    try:
        id = request.args.get("id")
        if int(id) > 5 or int(id) < 1:
            return redirect("/error_page?err=id_range.html")
        return render_template('cat.html', id=id)
    except:
        return redirect("/error_page?err=no_id.html")


@app.route('/error_page')
def error():
    error_status = request.args.get("err")
    err_temp_path = os.path.join('/var/www/flask/', 'error', error_status)
    with open(err_temp_path, "r") as f:
        content = f.read().strip()
    return render_template_string(sanitize(content))

if __name__ == '__main__':
    app.run(host='0.0.0.0', threaded=True)
