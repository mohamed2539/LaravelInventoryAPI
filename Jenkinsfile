pipeline { 
    agent any

    environment {
        DB_HOST = "127.0.0.1"
        DB_DATABASE = "tennewinventory"
        DB_USERNAME = "tenuser"
        DB_PASSWORD = "Password123!"
     	DEPLOY_SERVER = "mohamed@192.168.106.132"
        DEPLOY_PATH = "/var/www/html"
    }

    stages {
        stage('Checkout') {
            steps {
                echo 'My be every thing working will from here'
                git branch: 'main', credentialsId: 'github-credentials', url: 'https://github.com/mohamed2539/LaravelInventoryAPI.git'
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
                sh 'php artisan route:cache'
                sh 'php artisan view:cache'
            }
        }

        stage('Run Migrations') {
            steps {
                sh 'php artisan migrate --force'
            }
        }

        stage('Deploy Application') {
            steps {
                echo 'Deploying application using Rsync from DevOps Porject'
                sh '''
                      rsync -avz --delete -e "ssh -i /var/lib/jenkins/.ssh/id_rsa -o StrictHostKeyChecking=no" . ${DEPLOY_SERVER}:${DEPLOY_PATH}/
                      ssh -i /var/lib/jenkins/.ssh/id_rsa -o StrictHostKeyChecking=no ${DEPLOY_SERVER} "sudo chown -R www-data:www-data ${DEPLOY_PATH}/storage ${DEPLOY_PATH}/bootstrap/cache && sudo chmod -R 775 ${DEPLOY_PATH}/storage ${DEPLOY_PATH}/bootstrap/cache"
                      ssh -i /var/lib/jenkins/.ssh/id_rsa -o StrictHostKeyChecking=no ${DEPLOY_SERVER} "sudo systemctl restart apache2"

              
                '''
                echo 'Application Deployed Successfully via Rsync! 🚀'
            }
        }

        stage('Health Check') {
            steps {
                script {
                    def statusCode = sh(script: 'curl -o /dev/null -s -w "%{http_code}" http://192.168.106.132', returnStdout: true).trim()
                    if (statusCode != '200') {
                        error("Laravel is not responding correctly! Status: ${statusCode}")
                    }
                }
            }
        }

        stage('Send Notifications') {
            steps {
                echo 'Sending notifications...'
                emailext subject: "✅ Deployment Successful!", body: "Laravel deployment completed successfully.", to: 'mohamed.carinawear@gmail.com'
            }
        }
    }

    post {
        failure {
            emailext subject: "❌ Deployment Failed!", body: "Check Jenkins logs.", to: 'mohamed.carinawear@gmail.com'
        }
    }
}
