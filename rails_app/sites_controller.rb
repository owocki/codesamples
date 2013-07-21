class SitesController < ApplicationController
    include ActionView::Helpers::DateHelper


  # GET /sites
  # GET /sites.json
  def index
    @sites = Site.all

    respond_to do |format|
      format.html # index.html.erb
      format.json { render :json => @sites }
    end
  end

  # GET /sites/1
  # GET /sites/1.json
  def check
    index = params[:id].tr('_','.')

    # TODO: refactor the index splitter into its own module
    # .com domains will not even have a period in them
    if index.split('.').length < 2
      index = index+'.com'
    end

    @site = Site.find_by_domain(index)
    @site.recordLoadTime

    redirect_to @site.permalink_to

  end

  # GET /sites/1
  # GET /sites/1.json
  def show

    index = params[:id].tr('_','.')
    # TODO: refactor the index splitter into its own module
    # .com domains will not even have a period in them
    if index.split('.').length < 2
      index = index+'.com'
    end

    @site = Site.find_by_domain(index)

    # handle 404
    if @site == nil
      raise ActionController::RoutingError.new('Not Found')
      return 
    end

    # find popular sites to be displayed on sidebar
    @popularSites = Site.find(:all, :limit => 14 )
    @loadtimes = Siteloadtime.getLoadTimesForDomain(index)
    
    # set accessed time
    @site.accessed_at = Time.now
    @site.save
    puts @loadtimes.length
    if @loadtimes.length > 1
      
      #Build chart
      data_table = GoogleVisualr::DataTable.new
      # Add Column Headers
      data_table.new_column('string', 'Time' )
      data_table.new_column('number', 'Speed (s)')
      data_table.new_column('number', 'Downtime')

      # Add Rows and Values
      @loadtimes.reverse.each do | lt |
        timeAgo = format("%02d:%02d", lt.created_at.hour, lt.created_at.min)
        data_table.add_rows([
          [timeAgo, [lt.loadtime.round(2),10].min, lt.loadtime > 10 ? 10 : 0]
        ])
      end
      option = { width: 1100, height: 240, title: index+' load time' }
      @chart = GoogleVisualr::Interactive::AreaChart.new(data_table, option)
    end

    respond_to do |format|
      format.html # show.html.erb
      format.json { render :json => @site }
    end
  end


  # GET /sites/popular
  def popular
    @sites = Site.paginate(:page => params[:page], :per_page => 100)
    @name = 'Popular'

    respond_to do |format|
      format.html # show.html.erb
      format.json { render :json => @site }
    end
  end


  # GET /sites/down
  def down
    @sites = Site.where('onlineStatus = ? and updated_at > ? ', 'offline', 2.hours.ago ).paginate(:page => params[:page], :per_page => 100)
    @name = 'Down'

    render action: "popular"
  end


  # GET /sites/slow
  def slow
    @sites = Site.where('lastLoadTime > ? AND updated_at > ? ', 1, 2.hours.ago ).order('lastLoadTime DESC') .paginate(:page => params[:page], :per_page => 100)
    @name = 'Slow'

    render action: "popular"
  end


  # GET /sites/new
  # GET /sites/new.json
  def new
    @site = Site.new

    respond_to do |format|
      format.html # new.html.erb
      format.json { render :json => @site }
    end
  end

  # GET /sites/1/edit
  def edit
    @site = Site.find(params[:id])
  end

  # POST /sites
  # POST /sites.json
  def create
    @site = Site.new(params[:site])

    respond_to do |format|
      if @site.save
        format.html { redirect_to @site, :notice => 'Site was successfully created. Please allow a few moments for load time data to render.' }
        format.json { render :json => @site, :status => :created, :location => @site }
      else
        format.html { render :action => "new" }
        format.json { render :json => @site.errors, :status => :unprocessable_entity }
      end
    end
  end

  # PUT /sites/1
  # PUT /sites/1.json
  def update
    @site = Site.find(params[:id])

    respond_to do |format|
      if @site.update_attributes(params[:site])
        format.html { redirect_to @site, :notice => 'Site was successfully updated.' }
        format.json { head :no_content }
      else
        format.html { render :action => "edit" }
        format.json { render :json => @site.errors, :status => :unprocessable_entity }
      end
    end
  end

  # DELETE /sites/1
  # DELETE /sites/1.json
  def destroy
    @site = Site.find(params[:id])
    @site.destroy

    respond_to do |format|
      format.html { redirect_to sites_url }
      format.json { head :no_content }
    end
  end
end
