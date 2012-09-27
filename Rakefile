require 'fileutils'
require 'rake/packagetask'
require 'tempfile'

task :default => 'test:all'

begin

  require 'jasmine'
  load 'jasmine/tasks/jasmine.rake'
rescue LoadError
  task :jasmine do
    abort "Jasmine is not available. In order to run jasmine, you must: (sudo) gem install jasmine"
  end
end

namespace :test do

  desc 'Run all tests'
  task :all do
    Rake::Task['test:server'].invoke
    Rake::Task['jasmine:ci'].invoke
  end

  desc 'Run the PHPUnit suite'
  task :server do
    sh %{cd tests && phpunit}
  end

  desc 'Run the Jasmine server'
  task :jasmine do
    sh %{rake jasmine JASMINE_PORT=1337}
  end

end

desc "Updates the version in the 'plugin.ini' files. If given the version
parameter, it also updates the version in the 'version' file. Before updating
the metadata files."

task :version, [:version] do |t, args|
  if args[:version].nil?
    version = IO.readlines('version')[0].strip
  else
    version = args[:version]
    IO.write('version', version, :mode => 'w')
  end

  puts "updating plugin.ini and package.json to #{version}"

  tmp = Tempfile.new 'features'
  tmp.close
  puts "TMP = <#{tmp.path.inspect}>"

  FileUtils.mv 'plugin.ini', tmp.path, :verbose => true
  sh %{cat #{tmp.path} | sed -e 's/^version=".*"/version="#{version}"/' > plugin.ini}
end

class PackageTask < Rake::PackageTask
  def package_dir_path()
    "#{package_dir}/#{@name}"
  end
  def package_name
    @name
  end
  def basename
    @version ? "#{@name}-#{@version}" : @name
  end
  def tar_bz2_file
    "#{basename}.tar.bz2"
  end
  def tar_gz_file
    "#{basename}.tar.gz"
  end
  def tgz_file
    "#{basename}.tgz"
  end
  def zip_file
    "#{basename}.zip"
  end
end

PackageTask.new('FedoraConnector') do |p|
  p.version     = IO.readlines('version')[0].strip
  p.need_tar_gz = true
  p.need_zip    = true

  p.package_files.include('controllers/**/*.php')
  p.package_files.include('FedoraConnectorPlugin.php')
  p.package_files.include('forms/**/*.php')
  p.package_files.include('helpers/**/*.php')
  p.package_files.include('Importers/**/*.php')
  p.package_files.include('libraries/**/*.php')
  p.package_files.include('LICENSE')
  p.package_files.include('models/**.php')
  p.package_files.include('plugin.*')
  p.package_files.include('README.md')
  p.package_files.include('Renderers/**/*.php')
  p.package_files.include('routes.ini')
  p.package_files.include('views/**/*.css')
  p.package_files.include('views/**/*.js')
  p.package_files.include('views/**/*.php')
  p.package_files.include('views/**/*.png')
end

## desc 'This calls the Cakefile to minify the JS.'
## task :minify do
##   sh %{cake build}
## end

