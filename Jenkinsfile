pipeline { 
    agent any

    environment {
        DB_HOST = "127.0.0.1"
        DB_DATABASE = "tennewinventory"
        DB_USERNAME = "tenuser"
        DB_PASSWORD = "Password123!"
    }

    stages {
        stage('Checkout') {
            steps {
                echo 'Checking out source code...'
                checkout scm
            }
        }

        stage('Set Environment Variables') {
            steps {
                sh """
                    echo "APP_NAME=Laravel" > .env
                    echo "APP_ENV=production" >> .env
                    echo "APP_KEY=base64:tWKezMZKeU8w8P8/kybQr3spbbB2pvHOmUBfSIeVFFA=" >> .env
                    echo "APP_DEBUG=false" >> .env
                    echo "APP_URL=http://192.168.106.132" >> .env
                    echo "LOG_CHANNEL=single" >> .env
                    echo "LOG_DEPRECATIONS_CHANNEL=null" >> .env
                    echo "LOG_LEVEL=debug" >> .env
                    echo "DB_CONNECTION=mysql" >> .env
                    echo "DB_HOST=${DB_HOST}" >> .env
                    echo "DB_PORT=3306" >> .env
                    echo "DB_DATABASE=${DB_DATABASE}" >> .env
                    echo "DB_USERNAME=${DB_USERNAME}" >> .env
                    echo "DB_PASSWORD=${DB_PASSWORD}" >> .env
                """
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'composer install --no-interaction --prefer-dist'
            }
        }

        stage('Clear Cache') {
            steps {
                sh 'php artisan cache:clear'
                sh 'php artisan config:clear'
                sh 'php artisan config:cache'
            }
        }

        stage('Run Migrations') {
            steps {
                sh 'php artisan migrate --force'
            }
        }

        stage('Deploy Application') {
            steps {
                echo 'Deploying application...'

                // تعديل التصاريح عشان Jenkins يقدر يشغل التطبيق
                sh 'sudo chown -R jenkins:www-data storage bootstrap/cache'
                sh 'sudo chmod -R 775 storage bootstrap/cache'

                // عمل Restart للـ Apache2 عشان يطبق التغييرات
                sh 'sudo systemctl restart apache2'

                echo 'Application Deployed Successfully via Apache2! 🚀'
            }
        }

        stage('Send Notifications') {
            steps {
                echo 'Sending notifications...'
              // mohamed.carinawear@gmail.com

            }
        }
    }
}
