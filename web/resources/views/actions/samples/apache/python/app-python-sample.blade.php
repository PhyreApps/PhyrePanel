from flask import Flask, render_template
MyApp = Flask(__name__)

@MyApp.route("/")
def hello():
    return render_template('index.html')

if __name__ == "__main__":
    MyApp.run()
