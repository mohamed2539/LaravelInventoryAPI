pipeline {
    agent any
    
    environment {
        DB_HOST = "127.0.0.1"
        DB_DATABASE = "tennewinventory"
        DB_USERNAME = "tenuser"
        DB_PASSWORD = "password123"
    }

    stages {
        stage('Checkout') {
            steps {
                echo 'Checking out source code...'
                checkout scm
            }
        }

        stage('Install Dependencies') {
            steps {
                echo 'Installing dependencies...'
                sh 'composer install --no-interaction --prefer-dist'
            }
        }

        stage('Set Permissions') {
            steps {
                echo 'Setting permissions...'
                sh '''
                    sudo chown -R jenkins:jenkins storage bootstrap/cache
                    sudo chmod -R 775 storage bootstrap/cache
                '''
            }
        }

        stage('Clear Cache') {
            steps {
                echo 'Clearing cache...'
                sh 'sudo -u jenkins php artisan config:clear'
                sh 'sudo -u jenkins php artisan route:clear'
                sh 'sudo -u jenkins php artisan cache:clear'
            }
        }

        stage('Run Migrations') {
            steps {
                echo 'Running migrations...'
                sh 'sudo -u jenkins php artisan migrate --force'
            }
        }

        stage('Restart Server') {
            steps {
                echo 'Restarting server...'
                sh 'sudo systemctl restart apache2'
            }
        }
    }

    post {
        success {
            echo '✅ Build successful!'
        }
        failure {
            echo '❌ Build failed!'
        }
    }
}
